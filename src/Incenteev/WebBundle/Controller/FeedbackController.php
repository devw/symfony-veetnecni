<?php

namespace Incenteev\WebBundle\Controller;

use Incenteev\WebBundle\IncenteevEvents;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Controller managing the invitations
 */
class FeedbackController extends Controller
{
    /**
     * Sends the user feedback to the team.
     *
     * @param Request $request
     *
     * @throws HttpException
     * @return Response
     */
    public function sendAction(Request $request)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->container->get('event_dispatcher');
        $message = $request->request->get('message');

        $referrer = $request->headers->get('Referer');
        if (null === $referrer || 0 !== strpos($referrer, $request->getSchemeAndHttpHost())) {
            $referrer = $this->generateUrl('contest_list');
        }

        if (empty($message)) {
            if ($request->isXmlHttpRequest()) {
                throw new HttpException(412, 'The feedback message should not be empty');
            }

            $dispatcher->dispatch(IncenteevEvents::FEEDBACK_SEND_FAILURE);

            return new RedirectResponse($referrer);
        }

        $user = $this->getUser();

        /** @var $mailer \Incenteev\WebBundle\Mailer\MailerInterfaceı */
        $mailer = $this->get('incenteev.mailer');
        $mailer->send(
            $this->container->getParameter('incenteev.feedback.address'),
            'WebBundle:Mail:feedback.html.twig',
            array('message' => $message, 'sender' => $user, 'referrer' => $referrer),
            null,
            $user->getEmail()
        );

        if ($request->isXmlHttpRequest()) {
            return new Response('', 201);
        }

        $dispatcher->dispatch(IncenteevEvents::FEEDBACK_SEND_SUCCESS);

        return new RedirectResponse($referrer);
    }
}
