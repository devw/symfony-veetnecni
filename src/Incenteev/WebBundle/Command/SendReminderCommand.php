<?php

namespace Incenteev\WebBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendReminderCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('incenteev:send-reminder')
            ->addArgument('name', InputArgument::REQUIRED)
            ->setDescription('Sends a reminder.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('<info>Sending the "%s" reminder</info>', $input->getArgument('name')));

        /** @var $reminderManager \Incenteev\WebBundle\Reminder\ReminderManager */
        $reminderManager = $this->getContainer()->get('incenteev.reminder.manager');

        $reminderManager->sendReminders($input->getArgument('name'));
    }
}
