<?php

namespace Incenteev\WebBundle\EventListener;

use Incenteev\WebBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Listener responsible to set the locale
 */
class LocaleListener implements EventSubscriberInterface
{
    private $securityContext;
    private $locales;

    public function __construct(SecurityContextInterface $securityContext, array $locales)
    {
        $this->securityContext = $securityContext;
        $this->locales = $locales;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 5), // has to be called after the firewall
        );
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();

        $token = $this->securityContext->getToken();
        if (null === $token) {
            $this->setLocaleFromRequest($request);

            return;
        }

        /** @var $user User */
        $user = $token->getUser();
        if (!$user instanceof User) {
            $this->setLocaleFromRequest($request);

            return;
        }

        $request->setLocale($user->getOrganization()->getLanguage());
    }

    private function setLocaleFromRequest(Request $request)
    {
        $request->setLocale($request->getPreferredLanguage($this->locales));
    }
}
