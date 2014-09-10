<?php

namespace Incenteev\WebBundle\Controller\Admin;

use Incenteev\WebBundle\Avatar\PathResolverInterface;
use Incenteev\WebBundle\Entity\User;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends Controller
{
    public function indexAction(Request $request)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        $users = new Pagerfanta(new DoctrineORMAdapter($this->getUserRepository()->getMembersQueryBuilder($user->getOrganization()->getId()), false));
        $users->setCurrentPage($request->query->get('page', 1), true);
        $users->setMaxPerPage(20);

        return $this->render('WebBundle:Admin/User:index.html.twig', array(
            'users' => $users,
            'roles' => array(
                'ROLE_ADMIN' => 'user.role.admin',
                'ROLE_CONTEST_CREATOR' => 'user.role.contest_creator',
            ),
        ));
    }

    public function showAction($id)
    {
        /** @var $currentUser \Incenteev\WebBundle\Entity\User */
        $currentUser = $this->getUser();

        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUserRepository()->find($id);

        if (!$user) {
            throw $this->createNotFoundException(sprintf('Unable to find the user with id %s.', $id));
        }

        if ($currentUser->getOrganization() !== $user->getOrganization()) {
            throw new AccessDeniedException('You cannot view users of other organizations.');
        }

        return $this->render('WebBundle:Admin/User:show.html.twig', array(
            'user' => $user,
        ));
    }

    public function createAction(Request $request)
    {
        /** @var $currentUser \Incenteev\WebBundle\Entity\User */
        $currentUser = $this->getUser();

        $user = new User();
        $user->setOrganization($currentUser->getOrganization())
            ->setEnabled(false)
            ->setCreatedBy($currentUser)
            ->setPassword('');

        $form = $this->createForm('incenteev_admin_user', $user);

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $user->setConfirmationToken($tokenGenerator->generateToken());

                /** @var $mailer \Incenteev\WebBundle\Mailer\MailerInterface */
                $mailer = $this->get('incenteev.mailer');
                $mailer->send($user->getEmail(), 'WebBundle:Mail:organizationInvitation.html.twig', array('user' => $user));

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

                return $this->redirect($this->generateUrl('admin_user_show', array('id' => $user->getId())));
            }
        }

        return $this->render('WebBundle:Admin/User:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, $id)
    {
        /** @var $currentUser \Incenteev\WebBundle\Entity\User */
        $currentUser = $this->getUser();

        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUserRepository()->find($id);

        if (!$user) {
            throw $this->createNotFoundException(sprintf('Unable to find the user with id %s.', $id));
        }

        if ($currentUser->getOrganization() !== $user->getOrganization()) {
            throw new AccessDeniedException('You cannot view users of other organizations.');
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('incenteev_admin_user', $user);

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

                $em->flush();

                return $this->redirect($this->generateUrl('admin_user_show', array('id' => $id)));
            }
        }

        return $this->render('WebBundle:Admin/User:edit.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private function getDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\UserRepository
     */
    private function getUserRepository()
    {
        return $this->getDoctrine()->getRepository('WebBundle:User');
    }
}
