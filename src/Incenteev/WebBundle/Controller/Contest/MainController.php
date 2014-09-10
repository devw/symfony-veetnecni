<?php

namespace Incenteev\WebBundle\Controller\Contest;

use Incenteev\WebBundle\IncenteevEvents;
use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Event\FormEvent;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Contest main controller.
 *
 */
class MainController extends BaseController
{
    /**
     * Lists all Contest entities.
     *
     */
    public function indexAction()
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        $contests = $this->getContestRepository()->getUserContests($user);

        return $this->render('WebBundle:Contest/Main:index.html.twig', array(
            'contests' => $contests,
        ));
    }

    /**
     * Finds and displays a Contest entity.
     *
     */
    public function showAction($id)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        /** @var $contest \Incenteev\WebBundle\Entity\Contest */
        $contest = $this->getContestRepository()->find($id);

        if (!$contest) {
            throw $this->createNotFoundException('Unable to find Contest entity.');
        }

        if ($contest->getOrganization() !== $user->getOrganization()) {
            throw new AccessDeniedException('You cannot view contests of other organizations.');
        }

        if (!$contest->isPublished() && !$contest->hasOwner($user)) {
            throw new AccessDeniedException('Only contest owners can view an unpublished contest.');
        }

        $board = $this->getParticipationRepository()->getContestBoard($contest, 10, 0);

        return $this->render('WebBundle:Contest/Main:show.html.twig', array(
            'contest' => $contest,
            'board' => $board,
        ));
    }

    public function detailsAction(Request $request, $id)
    {
        /** @var $contest \Incenteev\WebBundle\Entity\Contest */
        $contest = $this->getContestRepository()->find($id);

        $participants = new Pagerfanta(new DoctrineORMAdapter($this->getParticipationRepository()->getContestParticipationsQueryBuilder($contest), false));
        $participants->setCurrentPage($request->query->get('page', 1), true);
        $participants->setMaxPerPage(20);

        return $this->render('WebBundle:Contest/Main:details.html.twig', array(
            'contest' => $contest,
            'participants' => $participants,
        ));
    }

    public function teamsRankAction($id)
    {
        /** @var $contest \Incenteev\WebBundle\Entity\Contest */
        $contest = $this->getContestRepository()->find($id);
        $teamBoard = $this->getContestTeamRepository()->getContestBoard($contest);

        return $this->render('WebBundle:Contest/Main:teamsRank.html.twig', array(
            'contest' => $contest,
            'team_board' => $teamBoard,
        ));
    }

    public function createAction()
    {
        /** @var $securityContext \Symfony\Component\Security\Core\SecurityContextInterface */
        $securityContext = $this->get('security.context');

        if (!$securityContext->isGranted('ROLE_CONTEST_CREATOR')) {
            throw new AccessDeniedException();
        }

        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        /** @var $periodicityGuesser \Incenteev\WebBundle\Contest\PeriodicityGuesser */
        $periodicityGuesser = $this->get('incenteev.contest.periodicity_guesser');

        $contest = new Contest();
        $contest->addOwner($user)
            ->setName('My contest') // TODO translate it ? It could make it harder to warn against using the generated name when publishing
            ->setStartDate(new \DateTime())
            ->setOrganization($user->getOrganization())
            ->setReminderPeriodicity($periodicityGuesser->getGuessedReminderPeriodicity($contest))
            ->setSummaryPeriodicity($periodicityGuesser->getGuessedSummaryPeriodicity($contest));

        $em->persist($contest->getReminderPeriodicity());
        $em->persist($contest->getSummaryPeriodicity());
        $em->persist($contest);
        $em->flush();

        $id = $contest->getId();

        /** @var $threadManager \FOS\CommentBundle\Model\ThreadManagerInterface */
        $threadManager = $this->container->get('fos_comment.manager.thread');
        $thread = $threadManager->createThread(sprintf('contest-%s', $id));
        $thread->setPermalink($this->generateUrl('contest_show', array('id' => $id), true));
        $thread->setCommentable(false);

        $threadManager->saveThread($thread);

        return $this->redirect($this->generateUrl('contest_settings_general', array('id' => $id)));
    }

    public function submitDataAction(Request $request, $id)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        /** @var $contest \Incenteev\WebBundle\Entity\Contest */
        $contest = $this->getContestRepository()->find($id);

        if (!$contest) {
            throw $this->createNotFoundException('Unable to find Contest entity.');
        }

        if (!$contest->isPublished()) {
            throw new AccessDeniedException('Impossible to submit data to an unpublished contest.');
        }

        if (!$contest->isUpdatedByParticipants()) {
            throw new AccessDeniedException('Results cannot be updated by participants in this contest.');
        }

        /** @var $participation \Incenteev\WebBundle\Entity\Participation */
        $participation = $this->getParticipationRepository()->findOneBy(array('user' => $user->getId(), 'contest' => $id));

        if (null === $participation) {
            // TODO handle this in a better way ?
            throw new AccessDeniedException('This user does not participate to the contest.');
        }

        if (!$participation->isAccepted()) {
            return $this->render('WebBundle:Contest/Main:notYetAccepted.html.twig', array(
                'participation' => $participation,
                'contest' => $contest,
            ));
        }

        /** @var $manager \Incenteev\WebBundle\Contest\DataEntryManager */
        $manager = $this->get('incenteev.contest.data_entry_manager');

        $entries = $manager->getEditableEntries($participation);

        $interval = null;
        if (Contest::GRANULARITY_DAY !== $contest->getGranularity()) {
            $interval = $manager->getGranularityInterval($contest);
        }

        $form = $this->createForm('incenteev_submit_data', $entries, array(
            'unit' => $contest->getUnit(),
            'interval' => $interval,
        ));

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                foreach ($entries as $entry) {
                    if (null === $entry->getValue()) {
                        $em->remove($entry);
                        continue;
                    }

                    $em->persist($entry);
                }

                $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_SUBMIT_ENTRY_SUCCESS, new FormEvent($form));

                $em->flush();

                return $this->redirect($this->generateUrl('contest_show', array('id' => $id)));
            }
        }

        $score = 0;

        foreach ($entries as $entry) {
            $score += $entry->getValue();
        }

        return $this->render('WebBundle:Contest/Main:submitData.html.twig', array(
            'contest' => $contest,
            'score' => $score,
            'form' => $form->createView(),
        ));
    }

    public function deleteAction($id)
    {
        $contest = $this->findContestForOwner($id);

        $this->getContestRepository()->deleteContest($contest);

        $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_DELETE_SUCCESS);

        return $this->redirect($this->generateUrl('contest_list'));
    }

}
