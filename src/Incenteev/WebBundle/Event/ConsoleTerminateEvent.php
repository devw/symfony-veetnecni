<?php

namespace Incenteev\WebBundle\Event;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleTerminateEvent extends ConsoleEvent
{
    private $exitCode;

    public function __construct(InputInterface $input, OutputInterface $output, $exitCode)
    {
        parent::__construct($input, $output);

        $this->exitCode = $exitCode;
    }

    public function getExitCode()
    {
        return $this->exitCode;
    }
}
