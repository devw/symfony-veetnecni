<?php

namespace Incenteev\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Entity\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Incenteev\WebBundle\Avatar\AvatarEnabledInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Incenteev\WebBundle\Entity\User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Incenteev\WebBundle\Doctrine\Repository\UserRepository")
 */
class User extends BaseUser implements AvatarEnabledInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default", "ProfileRegistration"})
     * @Assert\MaxLength(limit=255, groups={"Default", "ProfileRegistration"})
     * @ORM\Column(name="first_name", type="string", length=255)
     */
    private $firstName;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"AdminRegistration", "FullRegistration", "ProfileRegistration"})
     * @Assert\MaxLength(limit=255, groups={"Default", "ProfileRegistration"})
     * @ORM\Column(name="last_name", type="string", length=255)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatarPath;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\User")
     */
    private $createdBy;

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
     * @Assert\NotBlank(groups={"Default", "FullRegistration"})
     * @Assert\Valid()
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\Organization")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organization;

    /**
     * {@inheritDoc}
     */
    public function setEmail($email)
    {
        $this->setUsername($email);

        return parent::setEmail($email);
    }

    public function setRawRoles(array $roles)
    {
        foreach (array(self::ROLE_DEFAULT, self::ROLE_SUPER_ADMIN) as $specialRole) {
            if (in_array($specialRole, $this->roles)) {
                $roles[] = $specialRole;
            }
        }

        $this->setRoles($roles);
    }

    public function getRawRoles()
    {
        $specialRoles = array(self::ROLE_DEFAULT, self::ROLE_SUPER_ADMIN);

        return array_diff($this->roles, $specialRoles);
    }

    /**
     * Sets the first name.
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $this->normalizeName($firstName);

        return $this;
    }

    /**
     * Gets the first name.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Sets the last name.
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $this->normalizeName($lastName);

        return $this;
    }

    /**
     * Gets the last name.
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Gets the name.
     *
     * @return string
     */
    public function getName()
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    /**
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
     * @return string
     */
    public function getAvatarPath()
    {
        return $this->avatarPath;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setCreatedBy(User $user = null)
    {
        $this->createdBy = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set organization
     *
     * @param Organization $organization
     *
     * @return User
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

    private function normalizeName($string)
    {
        if (null === $string) {
            return null;
        }

        return mb_convert_case($string, MB_CASE_TITLE, 'UTF-8');
    }
}
