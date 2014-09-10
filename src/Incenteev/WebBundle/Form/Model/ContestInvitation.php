<?php

namespace Incenteev\WebBundle\Form\Model;

use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Validator as IncenteevAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class ContestInvitation
{
    public $contest;

    /**
     * @var \Incenteev\WebBundle\Entity\User[]
     */
    public $existingUsers;

    /**
     * @var array
     *
     * @Assert\All({
     *      @Assert\Email()
     * })
     * @IncenteevAssert\AvailableEmails()
     */
    public $invitedEmails = array();

    public function __construct(Contest $contest)
    {
        $this->contest = $contest;
        $this->existingUsers = new ArrayCollection();
    }
}
