<?php

namespace Incenteev\WebBundle\Controller\Contest;

use FOS\UserBundle\Model\UserInterface;
use Incenteev\WebBundle\Avatar\PathResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AccountStatusException;

/**
 * Contest main controller.
 *
 */
class ConfirmationController extends BaseController
{
    public function manageInvitationAction(Request $request, $id, $token)
    {
        $participation = $this->checkParticipationForCurrentUser($id, $token);

        switch ($request->query->get('target')) {
            case 'submit':
                $route = 'contest_submit_data';
                break;

            default:
                $route = 'contest_show';
        }

        if ($participation->isAccepted()) {
            return $this->redirect($this->generateUrl($route, array('id' => $id)));
        }

        $needPassword = !$participation->getUser()->isEnabled();
        $processRoute = $needPassword ? 'confirmation_with_registration' : 'confirmation_acceptance';

        return $this->redirect($this->generateUrl($processRoute, array(
            'id' => $id,
            'token' => $token,
            'full-process' => $needPassword ? true : null,
        )));
    }

    public function profileRegistrationAction(Request $request, $id, $token)
    {
        $participation = $this->checkParticipationForCurrentUser($id, $token);

        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $participation->getUser();

        $form = $this->createForm('incenteev_profile_registration', $user);

        $board = $this->getParticipationRepository()->getContestBoard($participation->getcontest(), 10, 0);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $user->setConfirmationToken(null);
                $user->setEnabled(true);
                $em->flush();

                $response = $this->redirect($this->generateUrl('confirmation_acceptance', array(
                    'id' => $id,
                    'token' => $token,
                    'full-process' => $request->query->get('full-process'),
                )));

                $this->authenticateUser($user, $response);

                return $response;
            }
        }

        return $this->render('WebBundle:Contest/Confirmation:profileRegistration.html.twig', array(
            'contest' => $participation->getContest(),
            'participation' => $participation,
            'form' => $form->createView(),
            'board' => $board,
        ));
    }

    public function confirmAction(Request $request, $id, $token)
    {
        $participation = $this->checkParticipationForCurrentUser($id, $token);

        $labelText = $participation->getContest()->getRules() ? 'confirmation.label.accept' : 'confirmation.label.accept_without_rules';
        $form = $this->createForm('incenteev_contest_accept', $participation, array('rules_label' => $labelText));

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $participation->setAccepted(true);
                $em->flush();

                $response = $this->redirect($this->generateUrl('confirmation_choose_avatar', array(
                    'id' => $id,
                    'token' => $token,
                    'full-process' => $request->query->get('full-process'),
                )));

                return $response;
            }
        }

        return $this->render('WebBundle:Contest/Confirmation:confirm.html.twig', array(
            'contest' => $participation->getContest(),
            'participation' => $participation,
            'form' => $form->createView(),
        ));
    }

    public function chooseAvatarAction(Request $request, $id, $token)
    {
        $participation = $this->getParticipationRepository()->getParticipationByToken($token, $id);

        if (null === $participation) {
            throw $this->createNotFoundException('Unable to find Participation entity.');
        }

        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        $formBuilder = $this->createFormBuilder($user);
        $formBuilder->add('avatar', 'incenteev_avatar', array(
            'label' => 'confirmation.label.avatar',
            'help_text' => 'confirmation.help.avatar',
        ));

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                $file = $form->get('avatar')->getData();

                if (null !== $file) {
                    /** @var $uploader \Incenteev\WebBundle\Avatar\Uploader */
                    $uploader = $this->get('incenteev.avatar.uploader');
                    $uploader->upload($participation->getUser(), PathResolverInterface::TYPE_AVATAR, file_get_contents($file->getPathname()));
                }

                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $response = $this->redirect($this->generateUrl('contest_show', array('id' => $participation->getContest()->getId())));

                $this->authenticateUser($participation->getUser(), $response);

                return $response;
            }
        }

        return $this->render('WebBundle:Contest/Confirmation:chooseAvatar.html.twig', array(
            'contest' => $participation->getContest(),
            'participation' => $participation,
            'form' => $form->createView(),
        ));
    }

    /**
     * @param int    $id
     * @param string $token
     *
     * @return \Incenteev\WebBundle\Entity\Participation
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    private function checkParticipationForCurrentUser($id, $token)
    {
        $participation = $this->getParticipationRepository()->getParticipationByToken($token, $id);

        if (null === $participation) {
            throw new NotFoundHttpException('Unable to find participation for this token.');
        }

        if (!$participation->getContest()->isPublished()) {
            throw new AccessDeniedException('Only contest owners can access an unpublished contest.');
        }

        if ($participation->isAccepted()) {
            throw new AccessDeniedException('You already register to this contest.');
        }

        return $participation;
    }

    /**
     * Authenticate a user with Symfony Security
     *
     * @param \FOS\UserBundle\Model\UserInterface        $user
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    private function authenticateUser(UserInterface $user, Response $response)
    {
        try {
            $this->container->get('fos_user.security.login_manager')->loginUser(
                $this->container->getParameter('fos_user.firewall_name'),
                $user,
                $response);
        } catch (AccountStatusException $ex) {
            // We simply do not authenticate users which do not pass the user
            // checker (not enabled, expired, etc.).
        }
    }
}
