<?php

namespace Incenteev\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller displaying the showcase
 */
class ShowcaseController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var $securityContext \Symfony\Component\Security\Core\SecurityContextInterface */
        $securityContext = $this->get('security.context');

        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirect($this->generateUrl('contest_list'));
        }

        //TODO - Uncomment this as soon as we want users in the app
        //$form = $this->container->get('fos_user.registration.form');
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('email', 'email', array(
            'required' => true,
            'constraints' => array(
                new Email(),
                new NotNull(),
            ),
            'attr' => array('placeholder' => 'form.placeholder.email'),
        ));

        $form = $formBuilder->getForm();

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                /** @var $mailer \Incenteev\WebBundle\Mailer\MailerInterface */
                $mailer = $this->get('incenteev.mailer');
                $email = $form->get('email')->getData();

                $mailer->send(
                    $this->container->getParameter('incenteev.feedback.address'),
                    'WebBundle:Mail:comingSoon.html.twig',
                    array('email' => $email)
                );

                return $this->redirect($this->generateUrl('showcase_coming_soon'));
            }
        }

        return $this->render('WebBundle:Showcase:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function subscriptionCompleteAction()
    {
        return $this->render('WebBundle:Showcase:comingSoon.html.twig', array());
    }
}
