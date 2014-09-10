<?php

namespace Incenteev\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Incenteev\WebBundle\Entity\Participation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Incenteev\WebBundle\Doctrine\Repository\ParticipationRepository")
 */
class Participation
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Contest
     *
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\Contest", inversedBy="participations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contest;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\User")
     */
    private $invitedBy;

    /**
     * @var User
     *
     * @Assert\Valid()
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var ContestTeam
     *
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\ContestTeam", inversedBy="participations")
     */
    private $contestTeam;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var boolean
     *
     * @Assert\True(groups={"accept"})
     * @ORM\Column(name="accepted", type="boolean")
     */
    private $accepted = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="change", field="accepted", value=true)
     */
    private $acceptedAt;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set contest
     *
     * @param Contest $contest
     *
     * @return Participation
     */
    public function setContest(Contest $contest)
    {
        $this->contest = $contest;

        return $this;
    }

    /**
     * Get contest
     *
     * @return Contest
     */
    public function getContest()
    {
        return $this->contest;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setInvitedBy(User $user = null)
    {
        $this->invitedBy = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getInvitedBy()
    {
        return $this->invitedBy;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Participation
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set contest team
     *
     * @param ContestTeam $team
     *
     * @return Participation
     */
    public function setContestTeam(ContestTeam $team = null)
    {
        $this->contestTeam = $team;

        return $this;
    }

    /**
     * Get contest team
     *
     * @return ContestTeam|null
     */
    public function getContestTeam()
    {
        return $this->contestTeam;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Participation
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set accepted
     *
     * @param boolean $accepted
     *
     * @return Participation
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isAccepted()
    {
        return $this->accepted;
    }
}
