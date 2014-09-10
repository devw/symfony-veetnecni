<?php

namespace Incenteev\WebBundle\EventListener;

use Incenteev\WebBundle\IncenteevEvents;
use Stampie\MailerInterface;
use Stampie\Extra\SpoolMailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Listener responsible to flush the mail queue.
 */
class SpoolMailerListener implements EventSubscriberInterface
{
    private $mailer;
    private $logger;

    public function __construct(MailerInterface $mailer, LoggerInterface $logger = null)
    {
        $this->mailer = $mailer;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            IncenteevEvents::CONSOLE_TERMINATE => 'flushSpool',
            KernelEvents::TERMINATE => 'flushSpool',
        );
    }

    public function flushSpool()
    {
        if (!$this->mailer instanceof SpoolMailer) {
            return;
        }

        try {
            $this->mailer->flushSpool();
        } catch (\Exception $e) {
            if (null !== $this->logger) {
                $this->logger->warn(sprintf('Failed sending emails: %s', $e->getMessage()));
            }
        }
    }
}
