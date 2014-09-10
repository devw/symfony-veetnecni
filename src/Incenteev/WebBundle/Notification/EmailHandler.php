<?php

namespace Incenteev\WebBundle\Notification;

use Incenteev\WebBundle\Entity\User;
use Incenteev\WebBundle\Mailer\MailerInterface;

/**
 * Handler sending notifications per mail.
 */
class EmailHandler implements HandlerInterface
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritDoc}
     */
    public function send($type, User $recipient, $data)
    {
        switch ($type) {
            case NotificationManager::TYPE_CONTEST_PUBLISHED:
                $template = 'WebBundle:Mail/Notification:contestPublished.html.twig';
                break;

            case NotificationManager::TYPE_COMMENT_REPLY:
                $template = 'WebBundle:Mail/Notification:commentReply.html.twig';
                break;

            default:
                return;
        }

        $this->mailer->send($recipient, $template, $data);
    }
}
