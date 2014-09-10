<?php

namespace Incenteev\WebBundle\Mailer;

use Incenteev\WebBundle\Entity\User;
use Stampie\Identity;
use Stampie\MailerInterface as StampieMailerInterface;

class Mailer implements MailerInterface
{
    private $mailer;
    private $twig;
    private $defaultSender;
    private $defaultSenderName;
    private $styleInliner;

    /**
     * @param StampieMailerInterface $mailer
     * @param \Twig_Environment      $twig
     * @param string                 $defaultSender
     * @param string                 $defaultSenderName
     * @param StyleInlinerInterface  $styleInliner
     */
    public function __construct(StampieMailerInterface $mailer, \Twig_Environment $twig, $defaultSender, $defaultSenderName, StyleInlinerInterface $styleInliner)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->defaultSender = $defaultSender;
        $this->defaultSenderName = $defaultSenderName;
        $this->styleInliner = $styleInliner;
    }

    /**
     * {@inheritDoc}
     */
    public function send($recipient, $templateName, array $context = array(), $senderName = null, $replyTo = null)
    {
        /** @var $template \Twig_Template */
        $template = $this->twig->loadTemplate($templateName);
        $context = $this->twig->mergeGlobals($context);

        $subject = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $htmlBody = $this->styleInliner->inlineStyle($htmlBody);

        $message = $this->createMessage($recipient, $subject, $textBody, $htmlBody, $senderName, $replyTo);

        $this->mailer->send($message);
    }

    /**
     * Creates the message with the different parts.
     *
     * @param string $recipient
     * @param string $subject
     * @param string $textBody
     * @param string $htmlBody
     * @param string $senderName
     * @param string $replyTo
     *
     * @return \Stampie\MessageInterface
     */
    private function createMessage($recipient, $subject, $textBody, $htmlBody, $senderName, $replyTo)
    {
        if ($recipient instanceof User) {
            $recipientIdentity = new Identity($recipient->getEmail());
            $recipientIdentity->setName($recipient->getName());
            $recipient = array($recipientIdentity);
        }

        $sender = new Identity($this->defaultSender);
        $sender->setName($senderName ?: $this->defaultSenderName);

        $message = new MutableMessage();

        $message->setSubject($subject)
            ->setFrom($sender)
            ->setTo($recipient)
            ->setReplyTo($replyTo)
            ->setText($textBody);

        if (!empty($htmlBody)) {
            $message->setHtml($htmlBody);
        }

        return $message;
    }
}
