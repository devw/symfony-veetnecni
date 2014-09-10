<?php

namespace Incenteev\WebBundle\Console;

use Incenteev\WebBundle\IncenteevEvents;
use Incenteev\WebBundle\Event\ConsoleEvent;
use Incenteev\WebBundle\Event\ConsoleTerminateEvent;
use Symfony\Bundle\FrameworkBundle\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Application dispatching events before and after running commands
 *
 * TODO: remove it once the feature is provided in Symfony itself (in 2.2)
 * @link https://github.com/symfony/symfony/pull/3889
 */
class Application extends BaseApplication
{
    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        // The shell has some special workflow
        if (true === $input->hasParameterOption(array('--shell', '-s'))) {
            return parent::doRun($input, $output);
        }

        $this->getKernel()->boot();

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->getKernel()->getContainer()->get('event_dispatcher');

        $initEvent = new ConsoleEvent($input, $output);
        $dispatcher->dispatch(IncenteevEvents::CONSOLE_INIT, $initEvent);

        $exitCode = parent::doRun($input, $output);

        $terminateEvent = new ConsoleTerminateEvent($input, $output, $exitCode);
        $dispatcher->dispatch(IncenteevEvents::CONSOLE_TERMINATE, $terminateEvent);

        return $exitCode;
    }
}
