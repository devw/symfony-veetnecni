<?php

namespace Incenteev\WebBundle\Form\EventListener;

use Incenteev\WebBundle\Util\NameGuesserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

/**
 * Listener guessing the name from the email address.
 */
class NameGuesserListener implements EventSubscriberInterface
{
    private $guesser;

    public function __construct(NameGuesserInterface $guesser)
    {
        $this->guesser = $guesser;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_BIND => array('postBind', 10));
    }

    public function postBind(FormEvent $event)
    {
        $form = $event->getForm();
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $form->getData();

        $this->guesser->guessNames($user);
    }
}
