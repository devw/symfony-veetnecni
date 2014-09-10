<?php

namespace Incenteev\WebBundle\Reminder;

class ReminderManager
{
    private $senders = array();

    public function addSender($name, SenderInterface $sender)
    {
        $this->senders[$name] = $sender;
    }

    /**
     * @param string $name
     *
     * @return SenderInterface
     *
     * @throws \InvalidArgumentException
     */
    public function getSender($name)
    {
        if (!isset($this->senders[$name])) {
            throw new \InvalidArgumentException(sprintf('The sender "%s" is not registered', $name));
        }

        return $this->senders[$name];
    }

    public function sendReminders($senderName)
    {
        $this->getSender($senderName)->sendReminders();
    }
}
