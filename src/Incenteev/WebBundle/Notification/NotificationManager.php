<?php

namespace Incenteev\WebBundle\Notification;

use Incenteev\WebBundle\Entity\User;

/**
 * Manager responsible to send notifications by calling different handlers.
 */
class NotificationManager
{
    const TYPE_CONTEST_PUBLISHED = 'contest_published';
    const TYPE_COMMENT_REPLY = 'comment_reply';

    private $emailHandler;

    /**
     * @param HandlerInterface $handler
     */
    public function __construct(HandlerInterface $handler)
    {
        $this->emailHandler = $handler;
    }

    /**
     * Sends a notification
     *
     * @param string $type      One of the TYPE_* constants
     * @param User[] $recipients
     * @param mixed  $data
     */
    public function send($type, array $recipients, $data)
    {
        foreach ($recipients as $recipient) {
            $this->emailHandler->send($type, $recipient, array_merge(array('recipient' => $recipient), $data));
        }
    }
}
