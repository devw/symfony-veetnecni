<?php

namespace Incenteev\WebBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Incenteev\WebBundle\Avatar\AvatarEnabledInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Incenteev\WebBundle\Doctrine\Repository\ContestTeamRepository")
 */
class ContestTeam implements AvatarEnabledInterface
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
     * @var string $name
     *
     * @Assert\NotBlank()
     * @Assert\MaxLength(limit=255)
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $avatarPath
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatarPath;

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
     * @var Contest
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\Contest")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contest;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Incenteev\WebBundle\Entity\Participation", mappedBy="contestTeam", fetch="EXTRA_LAZY")
     */
    private $participations;

    /**
     * @Assert\Image(
     *      mimeTypes={"image/png", "image/x-png", "image/jpeg", "image/pjpeg"},
     *      maxSize = "2M"
     * )
     */
    private $avatar;

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set avatarPath
     *
     * @param string $avatarPath
     *
     * @return self
     */
    public function setAvatarPath($avatarPath)
    {
        $this->avatarPath = $avatarPath;

        return $this;
    }

    /**
     * Get avatarPath
     *
     * @return string
     */
    public function getAvatarPath()
    {
        return $this->avatarPath;
    }

    /**
     * Set contest
     *
     * @param Contest $contest
     *
     * @return self
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
     * Add participations
     *
     * @param Participation $participation
     *
     * @return self
     */
    public function addParticipation(Participation $participation)
    {
        if ($this !== $participation->getContestTeam()) {
            $this->participations[] = $participation;
            $participation->setContestTeam($this);
        }

        return $this;
    }

    /**
     * Remove participations
     *
     * @param Participation $participation
     */
    public function removeParticipation(Participation $participation)
    {
        if ($this === $participation->getContestTeam()) {
            $this->participations->removeElement($participation);
            $participation->setContestTeam(null);
        }
    }

    /**
     * Get participations
     *
     * @return Collection
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }
}
