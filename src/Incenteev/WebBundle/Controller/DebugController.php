<?php

namespace Incenteev\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller used to run code that is not used from the web normally.
 *
 * This allows to have the profiler and the detailed error output as the console
 * does not trigger the profiler.
 */
class DebugController extends Controller
{
    public function indexAction()
    {
        /** @var $reminderManager \Incenteev\WebBundle\Reminder\ReminderManager */
        $reminderManager = $this->get('incenteev.reminder.manager');
        $reminderManager->sendReminders('summary');

        return new Response('<html><body>Hello</body></html>');
    }
}
