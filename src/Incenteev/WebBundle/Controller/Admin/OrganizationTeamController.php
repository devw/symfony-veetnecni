<?php

namespace Incenteev\WebBundle\Controller\Admin;

use Incenteev\WebBundle\Avatar\PathResolverInterface;
use Incenteev\WebBundle\Entity\User;
use Incenteev\WebBundle\Entity\OrganizationTeam;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class OrganizationTeamController extends Controller
{
    public function indexAction()
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        $teams = $this->getOrganizationTeamRepository()->findBy(array('organization' => $user->getOrganization()->getId()));

        return $this->render('WebBundle:Admin/OrganizationTeam:index.html.twig', array(
            'teams' => $teams,
        ));
    }

    public function showAction($id)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        /** @var $team \Incenteev\WebBundle\Entity\OrganizationTeam */
        $team = $this->getOrganizationTeamRepository()->find($id);

        if (!$team) {
            throw $this->createNotFoundException(sprintf('Unable to find the team with id %s.', $id));
        }

        if ($team->getOrganization() !== $user->getOrganization()) {
            throw new AccessDeniedException('You cannot view teams of other organizations.');
        }

        return $this->render('WebBundle:Admin/OrganizationTeam:show.html.twig', array(
            'team' => $team,
        ));
    }

    public function createAction(Request $request)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        $team = new OrganizationTeam();
        $team->setOrganization($user->getOrganization());

        $form = $this->createForm('incenteev_organization_team', $team, array('organization_id' => $user->getOrganization()->getId()));

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                $file = $form->get('avatar')->getData();

                if (null !== $file) {
                    /** @var $uploader \Incenteev\WebBundle\Avatar\Uploader */
                    $uploader = $this->get('incenteev.avatar.uploader');
                    $uploader->upload($team, PathResolverInterface::TYPE_AVATAR, file_get_contents($file->getPathname()));
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($team);
                $em->flush();

                return $this->redirect($this->generateUrl('admin_team_show', array('id' => $team->getId())));
            }
        }

        return $this->render('WebBundle:Admin/OrganizationTeam:create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction(Request $request, $id)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        /** @var $team \Incenteev\WebBundle\Entity\OrganizationTeam */
        $team = $this->getOrganizationTeamRepository()->find($id);

        if (!$team) {
            throw $this->createNotFoundException(sprintf('Unable to find the team with id %s.', $id));
        }

        if ($user->getOrganization() !== $team->getOrganization()) {
            throw new AccessDeniedException('You cannot view teams of other organizations.');
        }

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm('incenteev_organization_team', $team, array('organization_id' => $user->getOrganization()->getId()));

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $file \Symfony\Component\HttpFoundation\File\UploadedFile */
                $file = $form->get('avatar')->getData();

                if (null !== $file) {
                    /** @var $uploader \Incenteev\WebBundle\Avatar\Uploader */
                    $uploader = $this->get('incenteev.avatar.uploader');
                    $uploader->upload($team, PathResolverInterface::TYPE_AVATAR, file_get_contents($file->getPathname()));
                }

                $em->flush();

                return $this->redirect($this->generateUrl('admin_team_show', array('id' => $id)));
            }
        }

        return $this->render('WebBundle:Admin/OrganizationTeam:edit.html.twig', array(
            'team' => $team,
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
     * @return \Doctrine\ORM\EntityRepository
     */
    private function getOrganizationTeamRepository()
    {
        return $this->getDoctrine()->getRepository('WebBundle:OrganizationTeam');
    }
}
