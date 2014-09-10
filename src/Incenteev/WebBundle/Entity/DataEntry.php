<?php

namespace Incenteev\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Incenteev\WebBundle\Entity\DataEntry
 *
 * @ORM\Table(name="data_entry")
 * @ORM\Entity(repositoryClass="Incenteev\WebBundle\Doctrine\Repository\DataEntryRepository")
 */
class DataEntry
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="decimal", precision=15, scale=5)
     */
    private $value;

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
     * @var Participation
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\Participation")
     * @ORM\JoinColumn(nullable=false)
     */
    private $participation;

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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return DataEntry
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set value
     *
     * @param float|string $value
     *
     * @return DataEntry
     */
    public function setValue($value)
    {
        if (is_numeric($value)) {
            $value = number_format($value, 5, '.', '');
        }

        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set participation
     *
     * @param Participation $participation
     *
     * @return DataEntry
     */
    public function setParticipation(Participation $participation)
    {
        $this->participation = $participation;

        return $this;
    }

    /**
     * Get participation
     *
     * @return Participation
     */
    public function getParticipation()
    {
        return $this->participation;
    }
}
