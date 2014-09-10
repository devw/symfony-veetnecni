<?php

namespace Incenteev\WebBundle\EventListener;

use Incenteev\WebBundle\IncenteevEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FlashListener implements EventSubscriberInterface
{
    private static $messages = array(
        IncenteevEvents::CONTEST_DELETE_SUCCESS => 'contest.flash.successful_delete',
        IncenteevEvents::CONTEST_EDIT_GENERAL_SUCCESS => 'contest_settings.flash.successful_edit.general',
        IncenteevEvents::CONTEST_EDIT_DATA_SUCCESS => 'contest_settings.flash.successful_edit.data',
        IncenteevEvents::CONTEST_INVITE_SUCCESS => 'contest_settings.flash.successful_edit.participants',
        IncenteevEvents::CONTEST_EDIT_REMOVE_PARTICIPANTS_SUCCESS => 'contest_settings.flash.successful_edit.remove_participants',
        IncenteevEvents::CONTEST_EDIT_TEAMS_SUCCESS => 'contest_settings.flash.successful_edit.teams',
        IncenteevEvents::CONTEST_MANAGE_PRIZES_SUCCESS => 'contest_settings.flash.successful_edit.prizes',
        IncenteevEvents::CONTEST_PUBLISH_SUCCESS => 'contest_settings.flash.successful_edit.launched',
        IncenteevEvents::CONTEST_CLONE_SUCCESS => 'contest_settings.flash.successful_edit.cloned',
        IncenteevEvents::CONTEST_SUBMIT_ENTRY_SUCCESS => 'result_submission.flash.successful_data_submit',
        IncenteevEvents::FEEDBACK_SEND_SUCCESS => 'feedback.flash.sent',
    );

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $session;
    private $translator;

    public function __construct(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        $this->translator = $translator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            IncenteevEvents::CONTEST_DELETE_SUCCESS => 'addFlash',
            IncenteevEvents::CONTEST_EDIT_GENERAL_SUCCESS => 'addFlash',
            IncenteevEvents::CONTEST_EDIT_DATA_SUCCESS => 'addFlash',
            IncenteevEvents::CONTEST_INVITE_SUCCESS => 'addFlash',
            IncenteevEvents::CONTEST_EDIT_REMOVE_PARTICIPANTS_SUCCESS => 'addFlash',
            IncenteevEvents::CONTEST_EDIT_TEAMS_SUCCESS => 'addFlash',
            IncenteevEvents::CONTEST_MANAGE_PRIZES_SUCCESS => 'addFlash',
            IncenteevEvents::CONTEST_PUBLISH_ALREADY_DONE => 'onPublishAlreadyDone',
            IncenteevEvents::CONTEST_PUBLISH_SUCCESS => 'addFlash',
            IncenteevEvents::CONTEST_CLONE_SUCCESS => 'addFlash',
            IncenteevEvents::CONTEST_SUBMIT_ENTRY_SUCCESS => 'addFlash',
            IncenteevEvents::FEEDBACK_SEND_FAILURE => 'onFeedbackFailure',
            IncenteevEvents::FEEDBACK_SEND_SUCCESS => 'addFlash',
        );
    }

    public function addFlash(Event $event)
    {
        $name = $event->getName();

        if (!isset(self::$messages[$name])) {
            throw new \InvalidArgumentException(sprintf('Unknown event name "%s"', $name));
        }

        $this->session->getFlashBag()->add('success', $this->translator->trans(self::$messages[$name]));
    }

    public function onPublishAlreadyDone(Event $event)
    {
        $this->session->getFlashBag()->add('warning', $this->translator->trans('contest_settings.flash.already_launched'));
    }

    public function onFeedbackFailure(Event $event)
    {
        $this->session->getFlashBag()->add('warning', $this->translator->trans('feedback.flash.empty'));
    }
}
