<?php

namespace Incenteev\WebBundle;

/**
 * Contains all events thrown in the Incenteev WebBundle
 */
final class IncenteevEvents
{
    /**
     * The CONSOLE_INIT event occurs before running commands
     *
     * The event listener method receives a Incenteev\WebBundle\Event\ConsoleEvent
     * instance.
     *
     * @var string
     */
    const CONSOLE_INIT = 'incenteev.console.init';

    /**
     * The CONSOLE_TERMINATE event occurs after running commands
     *
     * The event listener method receives a Incenteev\WebBundle\Event\ConsoleTerminateEvent
     * instance.
     *
     * @var string
     */
    const CONSOLE_TERMINATE = 'incenteev.console.terminate';

    /**
     * The CONTEST_CLONE_SUCCESS event occurs when a contest is cloned successfully.
     *
     * The event listener method receives a Symfony\Component\EventDispatcher\Event
     * instance.
     *
     * @var string
     */
    const CONTEST_CLONE_SUCCESS = 'incenteev.contest.clone.success';

    /**
     * The CONTEST_DELETE_SUCCESS event occurs when a contest is deleted successfully.
     *
     * The event listener method receives a Symfony\Component\EventDispatcher\Event
     * instance.
     *
     * @var string
     */
    const CONTEST_DELETE_SUCCESS = 'incenteev.contest.delete.success';

    /**
     * The CONTEST_EDIT_GENERAL_SUCCESS event occurs when a contest is edited successfully.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\FormEvent
     * instance.
     *
     * @var string
     */
    const CONTEST_EDIT_GENERAL_SUCCESS = 'incenteev.contest.edit.general.success';

    /**
     * The CONTEST_EDIT_APPEARANCE_SUCCESS event occurs when the contest appearance is edited successfully.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\FormEvent
     * instance.
     *
     * @var string
     */
    const CONTEST_EDIT_APPEARANCE_SUCCESS = 'incenteev.contest.edit.appearance.success';

    /**
     * The CONTEST_EDIT_EMAIL_CONTENT_SUCCESS event occurs when the contest emails content are edited successfully.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\FormEvent
     * instance.
     *
     * @var string
     */
    const CONTEST_EDIT_EMAIL_CONTENT_SUCCESS = 'incenteev.contest.edit.email_content.success';

    /**
     * The CONTEST_EDIT_DATE_SUCCESS event occurs when contest data information are edited successfully.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\FormEvent
     * instance.
     *
     * @var string
     */
    const CONTEST_EDIT_DATA_SUCCESS = 'incenteev.contest.edit.data.success';

    /**
     * The CONTEST_EDIT_TEAMS_SUCCESS event occurs when contest teams are edited successfully.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\FormEvent
     * instance.
     *
     * @var string
     */
    const CONTEST_EDIT_TEAMS_SUCCESS = 'incenteev.contest.edit.teams.success';

    /**
     * The CONTEST_INVITE_SUCCESS event occurs when invitations are sent.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\FormEvent
     * instance.
     *
     * @var string
     */
    const CONTEST_INVITE_SUCCESS = 'incenteev.contest.invite.success';

    /**
     * The CONTEST_EDIT_REMOVE_PARTICIPANTS_SUCCESS event occurs when participants are deleted successfully.
     *
     * The event listener method receives a Symfony\Component\EventDispatcher\Event
     * instance.
     *
     * @var string
     */
    const CONTEST_EDIT_REMOVE_PARTICIPANTS_SUCCESS = 'incenteev.contest.edit.remove_participants.success';

    /**
     * The CONTEST_MANAGE_PRIZES_SUCCESS event occurs when prizes are submitted.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\FormEvent
     * instance.
     *
     * @var string
     */
    const CONTEST_MANAGE_PRIZES_SUCCESS = 'incenteev.contest.manage_prizes.success';

    /**
     * The CONTEST_PUBLISH_ALREADY_DONE event occurs when a contest is already published and attempted again.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\FormEvent
     * instance.
     *
     * @var string
     */
    const CONTEST_PUBLISH_ALREADY_DONE = 'incenteev.contest.publish.already_done';

    /**
     * The CONTEST_PUBLISH_SUCCESS event occurs when a contest is published successfully.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\ContestEvent
     * instance.
     *
     * @var string
     */
    const CONTEST_PUBLISH_SUCCESS = 'incenteev.contest.publish.success';

    /**
     * The CONTEST_SUBMIT_ENTRY_SUCCESS event occurs when a data entry is submitted.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\FormEvent
     * instance.
     *
     * @var string
     */
    const CONTEST_SUBMIT_ENTRY_SUCCESS = 'incenteev.contest.submit_entry.success';

    /**
     * The FEEDBACK_SEND_FAILURE event occurs when a feedback submission failed.
     *
     * The event listener method receives a Symfony\Component\EventDispatcher\Event
     * instance.
     *
     * @var string
     */
    const FEEDBACK_SEND_FAILURE = 'incenteev.feedback.send.failure';

    /**
     * The FEEDBACK_SEND_SUCCESS event occurs when a feedback is submitted successfully.
     *
     * The event listener method receives a Symfony\Component\EventDispatcher\Event
     * instance.
     *
     * @var string
     */
    const FEEDBACK_SEND_SUCCESS = 'incenteev.feedback.send.success';

    /**
     * The REGISTRATION_REGISTER event occurs when the user registers first.
     *
     * The event listener method receives a Incenteev\WebBundle\Event\UserEvent
     * instance.
     *
     * @var string
     * @todo Remove this event once FOSUserBundle provides its own event.
     */
    const REGISTRATION_REGISTER = 'incenteev.recognition.register';
}
