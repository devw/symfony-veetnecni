<?php

namespace Incenteev\WebBundle\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class OrganizationController extends Controller
{
    public function editAction(Request $request)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();
        $form = $this->createForm('incenteev_organization', $user->getOrganization());

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {

                $this->getDoctrine()->getManager()->flush();

                return $this->redirect($this->generateUrl('organization_edit'));
            }
        }

        return $this->render('WebBundle:Admin/Organization:edit.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }
}
