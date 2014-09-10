<?php

namespace Incenteev\WebBundle\EventListener;

use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;
use Incenteev\WebBundle\Entity\Organization;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listener responsible to manage the FOSUserBundle Registration
 */
class RegistrationListener implements EventSubscriberInterface
{
    private $container;
    private $registry;

    public function __construct(ManagerRegistry $registry, ContainerInterface $container)
    {
        $this->container = $container;
        $this->registry = $registry;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationInitialize(UserEvent $event)
    {
        /** @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->container->get('request');

        $organization = new Organization();
        $organization->setLanguage($request->getLocale());

        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $event->getUser();
        $user->setOrganization($organization);
        $user->addRole('ROLE_ADMIN');
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $event->getForm()->getData();
        $organization = $user->getOrganization();
        $this->registry->getManager()->persist($organization);
    }
}
