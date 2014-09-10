<?php

namespace Incenteev\WebBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Incenteev\WebBundle\Avatar\AvatarEnabledInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Incenteev\WebBundle\Entity\OrganizationTeam
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class OrganizationTeam implements AvatarEnabledInterface
{
    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $name
     *
     * @Assert\NotBlank()
     * @Assert\MaxLength(limit=255)
     * @ORM\Column(type="string", length=255)
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
     * @var Organization
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\Organization")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="Incenteev\WebBundle\Entity\User", fetch="EXTRA_LAZY")
     */
    private $members;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->members = new ArrayCollection();
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
     * Set organization
     *
     * @param Organization $organization
     *
     * @return self
     */
    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Get organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Add members
     *
     * @param User $members
     *
     * @return self
     */
    public function addMember(User $members)
    {
        $this->members[] = $members;

        return $this;
    }

    /**
     * Remove members
     *
     * @param User $members
     */
    public function removeMember(User $members)
    {
        $this->members->removeElement($members);
    }

    /**
     * Get members
     *
     * @return Collection
     */
    public function getMembers()
    {
        return $this->members;
    }
}
