<?php

namespace Incenteev\WebBundle\Controller;

use Incenteev\WebBundle\Avatar\PathResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller displaying the profile
 */
class ProfileController extends Controller
{
    public function showAction()
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        return $this->render('WebBundle:Profile:show.html.twig', array('user' => $user));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();
        $form = $this->createForm('incenteev_profile', $user);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                $file = $form->get('avatar')->getData();

                if (null !== $file) {
                    /** @var $uploader \Incenteev\WebBundle\Avatar\Uploader */
                    $uploader = $this->get('incenteev.avatar.uploader');
                    $uploader->upload($user, PathResolverInterface::TYPE_AVATAR, file_get_contents($file->getPathname()));
                }

                if ($form->get('remove_avatar')->getData()) {
                    $user->setAvatarPath(null);
                }

                $this->getDoctrine()->getManager()->flush();

                return $this->redirect($this->generateUrl('profile_show'));
            }
        }

        return $this->render('WebBundle:Profile:edit.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
}
