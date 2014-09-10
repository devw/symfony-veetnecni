<?php

namespace Incenteev\WebBundle\Mailer;

use Incenteev\WebBundle\Entity\User;

interface MailerInterface
{
    /**
     * Sends a mail to the recipient(s) using the template.
     *
     * The template must define 3 blocks: subject, body_text and body_html.
     *
     * @param string|User  $recipient    The recipient address
     * @param string       $templateName The name of the Twig template
     * @param array        $context      The context passed to the template
     * @param string|null  $senderName   The name of the sender
     * @param string|null  $replyTo      The replyTo address if the sender one should not be used
     *
     * @return void
     */
    public function send($recipient, $templateName, array $context = array(), $senderName = null, $replyTo = null);
}
