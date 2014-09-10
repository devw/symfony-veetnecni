<?php

namespace Incenteev\WebBundle\Mailer;

use FOS\UserBundle\Mailer\MailerInterface as FOSMailerInterface;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FOSMailer implements FOSMailerInterface
{
    private $mailer;
    private $router;
    private $confirmationTemplate;
    private $resettingTemplate;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router, $confirmationTemplate, $resettingTemplate)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->confirmationTemplate = $confirmationTemplate;
        $this->resettingTemplate = $resettingTemplate;
    }

    /**
     * {@inheritDoc}
     */
    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $url = $this->router->generate('fos_user_registration_confirm', array('token' => $user->getConfirmationToken()), true);
        $context = array(
            'user' => $user,
            'confirmationUrl' => $url
        );

        $this->sendMessage($this->confirmationTemplate, $context, $user);
    }

    /**
     * {@inheritDoc}
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $url = $this->router->generate('fos_user_resetting_reset', array('token' => $user->getConfirmationToken()), true);
        $context = array(
            'user' => $user,
            'confirmationUrl' => $url
        );
        $this->sendMessage($this->resettingTemplate, $context, $user);
    }

    protected function sendMessage($templateName, $context, $recipient)
    {
        $this->mailer->send($recipient, $templateName, $context);
    }
}
