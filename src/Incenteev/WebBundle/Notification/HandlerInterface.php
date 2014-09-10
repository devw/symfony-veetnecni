<?php

namespace Incenteev\WebBundle\Notification;

use Incenteev\WebBundle\Entity\User;

/**
 * Interface implemented by all handlers responsible to send a notification
 */
interface HandlerInterface
{
    /**
     * Sends a notification
     *
     * @param string $type      One of the NotificationManager::TYPE_* constants
     * @param User   $recipient
     * @param mixed  $data
     */
    public function send($type, User $recipient, $data);
}
