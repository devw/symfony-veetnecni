<?php

namespace Incenteev\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Incenteev\WebBundle\Entity\Periodicity
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Periodicity
{
    /* Constants representing weeks in the month */
    const WEEK_1 = 1;
    const WEEK_2 = 2;
    const WEEK_3 = 4;
    const WEEK_4 = 8;
    const WEEK_5 = 16;
    const ALL_WEEKS = 31;

    /* Constants representing days */
    const DAY_SUNDAY = 1;
    const DAY_MONDAY = 2;
    const DAY_TUESDAY = 4;
    const DAY_WEDNESDAY = 8;
    const DAY_THURSDAY = 16;
    const DAY_FRIDAY = 32;
    const DAY_SATURDAY = 64;
    const WEEK_DAYS = 62;
    const ALL_DAYS = 127;

    /* Constants representing hours */
    const HOUR_0 = 1;
    const HOUR_1 = 2;
    const HOUR_2 = 4;
    const HOUR_3 = 8;
    const HOUR_4 = 16;
    const HOUR_5 = 32;
    const HOUR_6 = 64;
    const HOUR_7 = 128;
    const HOUR_8 = 256;
    const HOUR_9 = 512;
    const HOUR_10 = 1024;
    const HOUR_11 = 2048;
    const HOUR_12 = 4096;
    const HOUR_13 = 8192;
    const HOUR_14 = 16384;
    const HOUR_15 = 32768;
    const HOUR_16 = 65536;
    const HOUR_17 = 131072;
    const HOUR_18 = 262144;
    const HOUR_19 = 524288;
    const HOUR_20 = 1048576;
    const HOUR_21 = 2097152;
    const HOUR_22 = 4194304;
    const HOUR_23 = 8388608;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $weeks = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="days", type="integer")
     */
    private $days = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="hours", type="integer")
     */
    private $hours = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $guessed = true;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $weeks
     *
     * @return self
     */
    public function setWeeks($weeks)
    {
        $this->weeks = $weeks;

        return $this;
    }

    /**
     * @return integer
     */
    public function getWeeks()
    {
        return $this->weeks;
    }

    /**
     * Set days
     *
     * @param integer $days
     *
     * @return Periodicity
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * Get days
     *
     * @return integer
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Set hours
     *
     * @param integer $hours
     *
     * @return Periodicity
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return integer
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param boolean $guessed
     *
     * @return self
     */
    public function setGuessed($guessed)
    {
        $this->guessed = $guessed;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isGuessed()
    {
        return $this->guessed;
    }

    public static function getWeekValues()
    {
        return array_keys(self::getWeekChoices());
    }

    public static function getWeekChoices()
    {
        return array(
            self::WEEK_1 => 'periodicity.week.w1',
            self::WEEK_2 => 'periodicity.week.w2',
            self::WEEK_3 => 'periodicity.week.w3',
            self::WEEK_4 => 'periodicity.week.w4',
            self::WEEK_5 => 'periodicity.week.w5',
        );
    }

    public static function getDayValues()
    {
        return array_keys(self::getDayChoices());
    }

    public static function getDayChoices()
    {
        return array(
            self::DAY_MONDAY => 'periodicity.day.monday',
            self::DAY_TUESDAY => 'periodicity.day.tuesday',
            self::DAY_WEDNESDAY => 'periodicity.day.wednesday',
            self::DAY_THURSDAY => 'periodicity.day.thursday',
            self::DAY_FRIDAY => 'periodicity.day.friday',
            self::DAY_SATURDAY => 'periodicity.day.saturday',
            self::DAY_SUNDAY => 'periodicity.day.sunday',
        );
    }

    public static function getHourValues()
    {
        return array_keys(self::getHourChoices());
    }

    public static function getHourChoices()
    {
        return array(
            self::HOUR_0 => 'periodicity.hour.h0',
            self::HOUR_1 => 'periodicity.hour.h1',
            self::HOUR_2 => 'periodicity.hour.h2',
            self::HOUR_3 => 'periodicity.hour.h3',
            self::HOUR_4 => 'periodicity.hour.h4',
            self::HOUR_5 => 'periodicity.hour.h5',
            self::HOUR_6 => 'periodicity.hour.h6',
            self::HOUR_7 => 'periodicity.hour.h7',
            self::HOUR_8 => 'periodicity.hour.h8',
            self::HOUR_9 => 'periodicity.hour.h9',
            self::HOUR_10 => 'periodicity.hour.h10',
            self::HOUR_11 => 'periodicity.hour.h11',
            self::HOUR_12 => 'periodicity.hour.h12',
            self::HOUR_13 => 'periodicity.hour.h13',
            self::HOUR_14 => 'periodicity.hour.h14',
            self::HOUR_15 => 'periodicity.hour.h15',
            self::HOUR_16 => 'periodicity.hour.h16',
            self::HOUR_17 => 'periodicity.hour.h17',
            self::HOUR_18 => 'periodicity.hour.h18',
            self::HOUR_19 => 'periodicity.hour.h19',
            self::HOUR_20 => 'periodicity.hour.h20',
            self::HOUR_21 => 'periodicity.hour.h21',
            self::HOUR_22 => 'periodicity.hour.h22',
            self::HOUR_23 => 'periodicity.hour.h23',
        );
    }
}
