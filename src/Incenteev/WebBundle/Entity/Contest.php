<?php

namespace Incenteev\WebBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Incenteev\WebBundle\Avatar\AvatarEnabledInterface;
use Incenteev\WebBundle\Avatar\BackgroundEnabledInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Incenteev\WebBundle\Entity\Contest
 *
 * @Assert\Callback(methods={"validateDates"}, groups={"Default", "general"})
 * @Assert\Callback(methods={"validateGeneratedName"}, groups={"warning"})
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Incenteev\WebBundle\Doctrine\Repository\ContestRepository")
 */
class Contest implements AvatarEnabledInterface, BackgroundEnabledInterface
{
    const GRANULARITY_DAY = 'day';
    const GRANULARITY_WEEK = 'week';
    const GRANULARITY_MONTH = 'month';

    const OVERLAY_DARK = 'dark';
    const OVERLAY_LIGHT = 'light';
    const OVERLAY_DEFAULT = self::OVERLAY_LIGHT;

    const BACKGROUND_LEFT = 'left';
    const BACKGROUND_CENTER = 'center';
    const BACKGROUND_RIGHT = 'right';
    const BACKGROUND_DEFAULT = self::BACKGROUND_LEFT;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"Default", "general"}, message="contest.name.not_blank")
     * @Assert\MaxLength(limit=255, groups={"Default", "general"})
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"warning"}, message="contest.description.not_blank")
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @Assert\NotBlank(groups={"warning"}, message="contest.rules.not_blank")
     * @ORM\Column(name="rules", type="text", nullable=true)
     */
    private $rules;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank(message="contest.start_date.not_blank")
     * @ORM\Column(name="start_date", type="date", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank(message="contest.end_date.not_blank")
     * @ORM\Column(name="end_date", type="date", nullable=true)
     */
    private $endDate;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getGranularityValues", groups={"Default", "data"})
     * @ORM\Column(type="string", length=255)
     */
    private $granularity = self::GRANULARITY_WEEK;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="contest.unit.not_blank")
     * @ORM\Column(name="unit", type="string", length=255, nullable=true)
     */
    private $unit;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="contest.data_name.not_blank")
     * @ORM\Column(name="data_name", type="string", length=255, nullable=true)
     */
    private $dataName;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $published = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $updatedByParticipants = true;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatarPath;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $backgroundPath;

    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $styles = array();

    /**
     * @var string
     *
     * @ORM\Column(name="invitation_text", type="text", nullable=true)
     */
    private $invitationText;

    /**
     * @var string
     * @ORM\Column(name="reminder_text", type="text", nullable=true)
     */
    private $reminderText;

    /**
     * @var string
     * @ORM\Column(name="summary_text", type="text", nullable=true)
     */
    private $summaryText;

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
     * @Gedmo\Timestampable(on="change", field="published", value=true)
     */
    private $publishedAt;

    /**
     * @var Periodicity|null
     *
     * @Assert\Valid()
     * @ORM\OneToOne(targetEntity="Incenteev\WebBundle\Entity\Periodicity")
     */
    private $reminderPeriodicity;

    /**
     * @var Periodicity|null
     *
     * @Assert\Valid()
     * @ORM\OneToOne(targetEntity="Incenteev\WebBundle\Entity\Periodicity")
     */
    private $summaryPeriodicity;

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
     * @Assert\Count(min=1)
     * @ORM\ManyToMany(targetEntity="Incenteev\WebBundle\Entity\User", fetch="EXTRA_LAZY")
     */
    private $owners;

    /**
     * @var Collection
     *
     * @Assert\Count(min=1, groups={"warning"}, minMessage="contest.participations.min_count")
     * @ORM\OneToMany(targetEntity="Incenteev\WebBundle\Entity\Participation", mappedBy="contest", fetch="EXTRA_LAZY")
     */
    private $participations;

    /**
     * @var Collection
     *
     * @Assert\Count(min=1, groups={"warning"}, minMessage="contest.prizes.min_count")
     * @ORM\OneToMany(targetEntity="Incenteev\WebBundle\Entity\Prize", mappedBy="contest", fetch="EXTRA_LAZY", indexBy="rank")
     */
    private $prizes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->owners = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->prizes = new ArrayCollection();
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
     * Set description
     *
     * @param string $description
     *
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set rules
     *
     * @param string $rules
     *
     * @return self
     */
    public function setRules($rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * Get rules
     *
     * @return string
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return self
     */
    public function setStartDate(\DateTime $startDate = null)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return self
     */
    public function setEndDate(\DateTime $endDate = null)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @param string $granularity
     *
     * @return self
     */
    public function setGranularity($granularity)
    {
        $this->granularity = $granularity;

        return $this;
    }

    /**
     * @return string
     */
    public function getGranularity()
    {
        return $this->granularity;
    }

    /**
     * Set unit
     *
     * @param string $unit
     *
     * @return self
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set dataName
     *
     * @param string $dataName
     *
     * @return self
     */
    public function setDataName($dataName)
    {
        $this->dataName = $dataName;

        return $this;
    }

    /**
     * Get dataName
     *
     * @return string
     */
    public function getDataName()
    {
        return $this->dataName;
    }

    /**
     * Set published
     *
     * @param boolean $published
     *
     * @return self
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * Get published
     *
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $updatedByParticipants
     *
     * @return self
     */
    public function setUpdatedByParticipants($updatedByParticipants)
    {
        $this->updatedByParticipants = $updatedByParticipants;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isUpdatedByParticipants()
    {
        return $this->updatedByParticipants;
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
     * @param string $backgroundPath
     *
     * @return self
     */
    public function setBackgroundPath($backgroundPath)
    {
        $this->backgroundPath = $backgroundPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getBackgroundPath()
    {
        return $this->backgroundPath;
    }

    /**
     * @param array $styles
     *
     * @return self
     */
    public function setStyles(array $styles)
    {
        $this->styles = $styles;

        return $this;
    }

    /**
     * @return array
     */
    public function getStyles()
    {
        return $this->styles;
    }

    /**
     * Set reminderPeriodicity
     *
     * @param Periodicity|null $reminderPeriodicity
     *
     * @return self
     */
    public function setReminderPeriodicity(Periodicity $reminderPeriodicity = null)
    {
        $this->reminderPeriodicity = $reminderPeriodicity;

        return $this;
    }

    /**
     * Get reminderPeriodicity
     *
     * @return Periodicity|null
     */
    public function getReminderPeriodicity()
    {
        return $this->reminderPeriodicity;
    }

    /**
     * @param Periodicity|null $summaryPeriodicity
     *
     * @return self
     */
    public function setSummaryPeriodicity(Periodicity $summaryPeriodicity = null)
    {
        $this->summaryPeriodicity = $summaryPeriodicity;

        return $this;
    }

    /**
     * @return Periodicity|null
     */
    public function getSummaryPeriodicity()
    {
        return $this->summaryPeriodicity;
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
     * Add owner
     *
     * @param User $owner
     *
     * @return self
     */
    public function addOwner(User $owner)
    {
        if (!$this->hasOwner($owner)) {
            $this->owners[] = $owner;
        }

        return $this;
    }

    /**
     * Remove owner
     *
     * @param User $owner
     *
     * @return self
     */
    public function removeOwner(User $owner)
    {
        $this->owners->removeElement($owner);

        return $this;
    }

    /**
     * Get owners
     *
     * @return Collection
     */
    public function getOwners()
    {
        return $this->owners;
    }

    /**
     * checks whether a user is an owner.
     *
     * @param User $user
     *
     * @return boolean
     */
    public function hasOwner(User $user)
    {
        return $this->owners->contains($user);
    }

    /**
     * Get owners
     *
     * @return Collection
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * Get prizes
     *
     * @return Collection
     */
    public function getPrizes()
    {
        return $this->prizes;
    }

    public static function getBackgroundPositionChoices()
    {
        return array(
            self::BACKGROUND_LEFT => 'contest.customization.background_position.left',
            self::BACKGROUND_CENTER => 'contest.customization.background_position.center',
            self::BACKGROUND_RIGHT => 'contest.customization.background_position.right',
        );
    }

    public static function getOverlayChoices()
    {
        return array(
            self::OVERLAY_DARK => 'contest.customization.overlay.dark',
            self::OVERLAY_LIGHT => 'contest.customization.overlay.light',
        );
    }

    /**
     * @param string $invitationText
     *
     * @return self
     */
    public function setInvitationText($invitationText)
    {
        $this->invitationText = $invitationText;

        return $this;
    }

    /**
     * Get the invitation mail text
     *
     * @return string
     */
    public function getInvitationText()
    {
        return $this->invitationText;
    }

    /**
     * @param string $reminderText
     *
     * @return self
     */
    public function setReminderText($reminderText)
    {
        $this->reminderText = $reminderText;

        return $this;
    }

    /**
     * Get the reminder mail text
     *
     * @return string
     */
    public function getReminderText()
    {
        return $this->reminderText;
    }

    /**
     * @param string $summaryText
     *
     * @return self
     */
    public function setSummaryText($summaryText)
    {
        $this->summaryText = $summaryText;

        return $this;
    }

    /**
     * Get the summary mail text
     *
     * @return string
     */
    public function getSummaryText()
    {
        return $this->summaryText;
    }

    public function isStarted()
    {
        return $this->startDate <= new \DateTime();
    }

    public static function getGranularityValues()
    {
        return array(
            self::GRANULARITY_DAY,
            self::GRANULARITY_WEEK,
            self::GRANULARITY_MONTH,
        );
    }

    public static function getGranularityChoices()
    {
        return array(
            self::GRANULARITY_DAY => 'contest.granularity.day',
            self::GRANULARITY_WEEK => 'contest.granularity.week',
            self::GRANULARITY_MONTH => 'contest.granularity.month',
        );
    }

    /**
     * Validates that the dates are properly ordered
     *
     * @param ExecutionContext $context
     */
    public function validateDates(ExecutionContext $context)
    {
        if (null === $this->startDate || null === $this->endDate) {
            return;
        }

        if ($this->endDate < $this->startDate) {
            $context->addViolationAtSubPath('endDate', 'contest.date_order', array(), $this->endDate);
        }
    }

    /**
     * Validates that the name has been changed
     *
     * @param ExecutionContext $context
     */
    public function validateGeneratedName(ExecutionContext $context)
    {
        if ('My contest' === $this->name) {
            $context->addViolationAtSubPath('name', 'contest.name.unchanged', array(), $this->name);
        }
    }
}
