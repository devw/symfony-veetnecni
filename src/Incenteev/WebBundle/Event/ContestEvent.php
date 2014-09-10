<?php

namespace Incenteev\WebBundle\Event;

use Incenteev\WebBundle\Entity\Contest;
use Symfony\Component\EventDispatcher\Event;

class ContestEvent extends Event
{
    private $contest;

    public function __construct(Contest $contest)
    {
        $this->contest = $contest;
    }

    public function getContest()
    {
        return $this->contest;
    }
}
