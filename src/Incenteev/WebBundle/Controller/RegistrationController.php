<?php

namespace Incenteev\WebBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Incenteev\WebBundle\Avatar\PathResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegistrationController extends Controller
{
    public function confirmAction(Request $request, $token)
    {
        $user = $this->get('fos_user.user_manager')->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        $form = $this->createForm('incenteev_invited_registration', $user);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                $user->setConfirmationToken(null);
                $user->setEnabled(true);

                /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                $file = $form->get('avatar')->getData();

                if (null !== $file) {
                    /** @var $uploader \Incenteev\WebBundle\Avatar\Uploader */
                    $uploader = $this->get('incenteev.avatar.uploader');
                    $uploader->upload($user, PathResolverInterface::TYPE_AVATAR, file_get_contents($file->getPathname()));
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $response = $this->redirect($this->generateUrl('contest_list'));
                $this->authenticateUser($user, $response);

                return $response;
            }
        }

        return $this->render('WebBundle:Registration:confirm.html.twig', array(
            'form' => $form->createView(),
            'token' => $token,
        ));
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
