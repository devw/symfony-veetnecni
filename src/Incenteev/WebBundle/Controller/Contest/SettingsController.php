<?php

namespace Incenteev\WebBundle\Controller\Contest;

use Incenteev\WebBundle\Avatar\PathResolverInterface;
use Incenteev\WebBundle\Entity\Periodicity;
use Incenteev\WebBundle\Form\Model\ContestInvitation;
use Incenteev\WebBundle\IncenteevEvents;
use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Entity\User;
use Incenteev\WebBundle\Event\FormEvent;
use Incenteev\WebBundle\Validator\DistinctRanks;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Contest settings controller.
 *
 */
class SettingsController extends BaseController
{
    public function summaryAction($id)
    {
        $contest = $this->findContestForOwner($id, 'Only contests owners can edit a contest.');

        /** @var $validator \Symfony\Component\Validator\ValidatorInterface */
        $validator = $this->get('validator');

        $errors = $validator->validate($contest);
        $warnings = $validator->validate($contest, array('warning'));

        return $this->render('WebBundle:Contest/Settings:summary.html.twig', array(
            'contest' => $contest,
            'errors' => $errors,
            'warnings' => $warnings,
        ));
    }

    public function publishAction($id)
    {
        $contest = $this->findContestForOwner($id, 'Only contests owners can publish a contest.');

        if ($contest->isPublished()) {
            // TODO add the contest in the event
            $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_PUBLISH_ALREADY_DONE);

            return $this->redirect($this->generateUrl('contest_show', array('id' => $id)));
        }

        // TODO validate the contest before publishing it.

        /** @var $publisher \Incenteev\WebBundle\Contest\Publisher */
        $publisher = $this->get('incenteev.contest.publisher');

        $publisher->publish($contest);

        return $this->redirect($this->generateUrl('contest_show', array('id' => $id)));
    }

    public function cloneAction($id)
    {
        /** @var $securityContext \Symfony\Component\Security\Core\SecurityContextInterface */
        $securityContext = $this->get('security.context');

        if (!$securityContext->isGranted('ROLE_CONTEST_CREATOR')) {
            throw new AccessDeniedException();
        }

        $contest = $this->getContestRepository()->getFullContest($id);

        if (!$contest) {
            throw new NotFoundHttpException(sprintf('Unable to find contest with id %s.', $id));
        }

        $this->checkContestOwnerShip($contest, 'Only contests owners can clone a contest.');

        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        /** @var $contestCloner \Incenteev\WebBundle\Contest\Cloner */
        $contestCloner = $this->get('incenteev.contest.cloner');

        $newContest = $contestCloner->cloneContest($contest);

        $em->flush();

        $newId = $newContest->getId();

        /** @var $threadManager \FOS\CommentBundle\Model\ThreadManagerInterface */
        $threadManager = $this->container->get('fos_comment.manager.thread');
        $thread = $threadManager->createThread(sprintf('contest-%s', $newId));
        $thread->setPermalink($this->generateUrl('contest_show', array('id' => $newId), true));
        $thread->setCommentable(false);

        $threadManager->saveThread($thread);

        $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_CLONE_SUCCESS);

        return $this->redirect($this->generateUrl('contest_settings_general', array('id' => $newId)));
    }

    public function inviteAction(Request $request, $id)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        $contest = $this->getContestRepository()->getContestWithParticipants($id);

        if (!$contest) {
            throw new NotFoundHttpException(sprintf('Unable to find contest with id %s.', $id));
        }

        $this->checkContestOwnerShip($contest, 'Only contests owners can invite other people.');

        $invitations = new ContestInvitation($contest);
        $invitations->invitedEmails = array_fill(0, 2, null);

        $form = $this->createForm('incenteev_contest_invitation', $invitations, array(
            'organization_id' => $user->getOrganization()->getId(),
        ));

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                if (!$contest->hasOwner($user)) {
                    // Force the user to stay an owner. You cannot remove yourself.
                    $contest->addOwner($user);
                }

                /** @var $invitationManager \Incenteev\WebBundle\Contest\InvitationManager */
                $invitationManager = $this->get('incenteev.contest.invitation_manager');

                $invitationManager->processInvitations($contest, $invitations, $user);

                $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_INVITE_SUCCESS, new FormEvent($form));

                $route = $request->request->has('continue') ? 'contest_settings_teams' : 'contest_settings_invite';

                return $this->redirect($this->generateUrl($route, array('id' => $id)));
            }
        }

        $participants = new Pagerfanta(new DoctrineORMAdapter($this->getUserRepository()->getParticipantsQuery($contest), false));
        $participants->setCurrentPage($request->query->get('page', 1), true);
        $participants->setMaxPerPage(20);

        return $this->render('WebBundle:Contest/Settings:invite.html.twig', array(
            'contest' => $contest,
            'participants' => $participants,
            'form' => $form->createView(),
        ));
    }

    public function deleteParticipantsAction(Request $request, $id)
    {
        $contest = $this->findContestForOwner($id);

        /** @var $csrfProvider \Symfony\Component\Form\Extension\Csrf\CsrfProvider\CsrfProviderInterface */
        $csrfProvider = $this->get('form.csrf_provider');

        if ($csrfProvider->isCsrfTokenValid('delete_participants', $request->request->get('_token'))) {
            $userIds = (array) $request->request->get('user_ids', array());
            $this->getParticipationRepository()->deleteUserParticipations($contest, $userIds);

            $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_EDIT_REMOVE_PARTICIPANTS_SUCCESS);
        }

        return $this->redirect($this->generateUrl('contest_settings_invite', array('id' => $id, 'page' => $request->query->get('page'))));
    }

    /**
     * Edits an existing Contest entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $contest = $this->findContestForOwner($id);

        $editForm = $this->createForm('incenteev_contest', $contest, array('strict_edit' => $contest->isPublished()));

        if ($request->isMethod('POST')) {
            $editForm->bind($request);

            if ($editForm->isValid()) {
                $em = $this->getDoctrine()->getManager();
                /** @var $uploader \Incenteev\WebBundle\Avatar\Uploader */
                $uploader = $this->get('incenteev.avatar.uploader');

                $em->persist($contest);

                /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                $file = $editForm->get('avatar')->getData();

                if (null !== $file) {
                    $uploader->upload($contest, PathResolverInterface::TYPE_AVATAR, file_get_contents($file->getPathname()));
                }

                if ($editForm->get('remove_avatar')->getData()) {
                    $contest->setAvatarPath(null);
                }

                $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_EDIT_GENERAL_SUCCESS, new FormEvent($editForm));

                $em->flush();

                $route = $request->request->has('continue') ? 'contest_settings_appearance' : 'contest_settings_general';

                return $this->redirect($this->generateUrl($route, array('id' => $id)));
            }
        }

        return $this->render('WebBundle:Contest/Settings:general.html.twig', array(
            'contest'     => $contest,
            'edit_form'   => $editForm->createView(),
        ));
    }

    public function prizesAction(Request $request, $id)
    {
        /** @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getDoctrine()->getManager();

        $contest = $this->findContestForOwner($id, 'Only contests owners can manage prizes.');

        $prizes = $em->getRepository('WebBundle:Prize')->findBy(array('contest' => $contest->getId()), array('rank' => 'asc'));

        if (empty($prizes)) {
            $prizes = array(null);
        }

        $form = $this->createForm('collection', $prizes, array(
            'type' => 'incenteev_prize',
            'allow_add' => true,
            'allow_delete' => true,
            'constraints' => new DistinctRanks(),
            'attr' => array('data-tid' => 'prize-widget'),
            'options' => array('contest' => $contest, 'required' => false, 'label' => false),
        ));

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $uploader \Incenteev\WebBundle\Avatar\Uploader */
                $uploader = $this->get('incenteev.avatar.uploader');

                /** @var $updatedPrizes \Incenteev\WebBundle\Entity\Prize[] */
                $updatedPrizes = $form->getData();

                foreach ($form as $child) {
                    if ($child->get('details')->get('remove_avatar')->getData()) {
                        $child->getData()->setAvatarPath(null);
                    }
                }

                foreach ($updatedPrizes as $prize) {
                    if (null !== $prize) {
                        $em->persist($prize);

                        /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                        $file = $prize->getAvatar();

                        if (null !== $file) {
                            $uploader->upload($prize, PathResolverInterface::TYPE_AVATAR, file_get_contents($file->getPathname()));
                        }
                    }
                }

                // handle removed teams
                foreach ($prizes as $prize) {
                    if (null === $prize) {
                        continue;
                    }

                    if (!in_array($prize, $updatedPrizes, true)) {
                        $em->remove($prize);
                    }
                }

                $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_MANAGE_PRIZES_SUCCESS, new FormEvent($form));
                $em->flush();

                $route = $request->request->has('continue') ? 'contest_settings_invite' : 'contest_settings_prizes';

                return $this->redirect($this->generateUrl($route, array('id' => $id)));
            }
        }

        return $this->render('WebBundle:Contest/Settings:prizes.html.twig', array(
            'contest' => $contest,
            'form' => $form->createView(),
        ));
    }

    public function dataAction(Request $request, $id)
    {
        $contest = $this->findContestForOwner($id);

        $form = $this->createForm('incenteev_contest_data', $contest, array('strict_edit' => $contest->isPublished()));

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($contest);

                /** @var $periodicityGuesser \Incenteev\WebBundle\Contest\PeriodicityGuesser */
                $periodicityGuesser = $this->get('incenteev.contest.periodicity_guesser');
                $periodicityGuesser->updateGuessedPeriodicity($contest);

                $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_EDIT_DATA_SUCCESS, new FormEvent($form));

                $em->flush();

                $route = $request->request->has('continue') ? 'contest_settings_email_content' : 'contest_settings_data';

                return $this->redirect($this->generateUrl($route, array('id' => $id)));
            }
        }

        return $this->render('WebBundle:Contest/Settings:data.html.twig', array(
            'contest' => $contest,
            'form' => $form->createView(),
        ));
    }

    public function appearanceAction(Request $request, $id)
    {
        $contest = $this->findContestForOwner($id);

        $form = $this->createForm('incenteev_contest_appearance', $contest);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                /** @var $uploader \Incenteev\WebBundle\Avatar\Uploader */
                $uploader = $this->get('incenteev.avatar.uploader');

                $em->persist($contest);

                $background = $form->get('background');
                /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                $file = $background->get('new')->getData();

                if (null !== $file) {
                    $uploader->upload($contest, PathResolverInterface::TYPE_BACKGROUND, file_get_contents($file->getPathname()));
                } elseif (null !== $builtin = $background->get('builtin')->getData()) {
                    // TODO remove the old uploaded background from the S3
                    $contest->setBackgroundPath($builtin);
                }

                if ($background->get('remove_background')->getData()) {
                    $contest->setBackgroundPath(null);
                }

                $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_EDIT_APPEARANCE_SUCCESS, new FormEvent($form));

                $em->flush();

                $route = $request->request->has('continue') ? 'contest_settings_prizes' : 'contest_settings_appearance';

                return $this->redirect($this->generateUrl($route, array('id' => $id)));
            }
        }

        return $this->render('WebBundle:Contest/Settings:appearance.html.twig', array(
            'contest' => $contest,
            'form' => $form->createView(),
        ));
    }

    public function teamsAction(Request $request, $id)
    {
        $contest = $this->findContestForOwner($id);
        $teams = $this->getContestTeamRepository()->getContestTeamsWithParticipants($contest);

        $form = $this->createForm('incenteev_contest_team_collection', $teams, array('contest' => $contest));

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                /** @var $uploader \Incenteev\WebBundle\Avatar\Uploader */
                $uploader = $this->get('incenteev.avatar.uploader');

                /** @var $updatedTeams \Incenteev\WebBundle\Entity\ContestTeam[] */
                $updatedTeams = $form->getData();

                foreach ($updatedTeams as $team) {
                    if (null !== $team) {
                        $em->persist($team);
                    }

                    /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                    $file = $team->getAvatar();

                    if (null !== $file) {
                        $uploader->upload($team, PathResolverInterface::TYPE_AVATAR, file_get_contents($file->getPathname()));
                    }
                }

                // handle removed teams
                foreach ($teams as $team) {
                    if (null === $team) {
                        continue;
                    }

                    if (!in_array($team, $updatedTeams, true)) {
                        foreach ($team->getParticipations() as $participation) {
                            /** @var $participation \Incenteev\WebBundle\Entity\Participation */
                            $participation->setContestTeam(null);
                        }

                        $em->remove($team);
                    }
                }

                $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_EDIT_TEAMS_SUCCESS, new FormEvent($form));

                $em->flush();

                $route = $request->request->has('continue') ? 'contest_settings_data' : 'contest_settings_teams';

                return $this->redirect($this->generateUrl($route, array('id' => $id)));
            }
        }

        return $this->render('WebBundle:Contest/Settings:teams.html.twig', array(
            'contest' => $contest,
            'form' => $form->createView(),
        ));
    }

    public function emailContentAction(Request $request, $id)
    {
        $contest = $this->findContestForOwner($id);

        $reminderPeriodicity = $this->getSavedPeriodicity($contest->getReminderPeriodicity());
        $summaryPeriodicity = $this->getSavedPeriodicity($contest->getSummaryPeriodicity());

        $form = $this->createForm('incenteev_contest_email_content', $contest);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $em->persist($contest);

                $periodicity = $contest->getReminderPeriodicity();
                if (null !== $periodicity) {
                    if (null === $reminderPeriodicity
                        || $periodicity->getWeeks() !== $reminderPeriodicity->getWeeks()
                        || $periodicity->getDays() !== $reminderPeriodicity->getDays()
                        || $periodicity->getHours() !== $reminderPeriodicity->getHours()
                    ) {
                        $periodicity->setGuessed(false);
                    }

                    $em->persist($periodicity);
                }

                $periodicity = $contest->getSummaryPeriodicity();
                if (null !== $periodicity) {
                    if (null === $summaryPeriodicity
                        || $periodicity->getWeeks() !== $summaryPeriodicity->getWeeks()
                        || $periodicity->getDays() !== $summaryPeriodicity->getDays()
                        || $periodicity->getHours() !== $summaryPeriodicity->getHours()
                    ) {
                        $periodicity->setGuessed(false);
                    }

                    $em->persist($periodicity);
                }

                $this->getDispatcher()->dispatch(IncenteevEvents::CONTEST_EDIT_EMAIL_CONTENT_SUCCESS, new FormEvent($form));

                $em->flush();

                $route = $request->request->has('continue') ? 'contest_settings_summary' : 'contest_settings_email_content';

                return $this->redirect($this->generateUrl($route, array('id' => $id)));
            }
        }

        return $this->render('WebBundle:Contest/Settings:emailContent.html.twig', array(
            'contest' => $contest,
            'form' => $form->createView(),
        ));
    }

    private function getSavedPeriodicity(Periodicity $periodicity = null)
    {
        if (null === $periodicity) {
            return null;
        }

        $em = $this->getDoctrine()->getManager();

        $em->initializeObject($periodicity);

        return clone $periodicity;
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\UserRepository
     */
    private function getUserRepository()
    {
        return $this->getDoctrine()->getRepository('WebBundle:User');
    }
}
