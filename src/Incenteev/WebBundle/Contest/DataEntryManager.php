<?php

namespace Incenteev\WebBundle\Contest;

use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Entity\DataEntry;
use Incenteev\WebBundle\Entity\Participation;
use Doctrine\Common\Persistence\ManagerRegistry;

class DataEntryManager
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param Contest $contest
     *
     * @return \DateInterval
     *
     * @throws \LogicException
     */
    public function getGranularityInterval(Contest $contest)
    {
        switch ($contest->getGranularity()) {
            case Contest::GRANULARITY_DAY:
                return \DateInterval::createFromDateString('1 day');

            case Contest::GRANULARITY_WEEK:
                return \DateInterval::createFromDateString('1 week');

            case Contest::GRANULARITY_MONTH:
                return \DateInterval::createFromDateString('1 month');

            default:
                throw new \LogicException(sprintf('Unsupported granularity "%s"', $contest->getGranularity()));
        }
    }

    public function getAllDates(Contest $contest, \DateTime $maxDate = null)
    {
        $startDate = $contest->getStartDate();
        $endDate = $contest->getEndDate();

        if (null === $startDate || null === $endDate) {
            return array();
        }

        if (null !== $maxDate) {
            $endDate = min($endDate, $maxDate);
        }

        $dates = array();

        $currentDate = $this->getFirstDate($contest);
        $interval = $this->getGranularityInterval($contest);

        while ($currentDate <= $endDate) {
            $dates[] = clone $currentDate;
            $currentDate->add($interval);
        }

        return $dates;
    }

    /**
     * @param Contest $contest
     *
     * @return \DateTime[]
     */
    public function getAllPastDates(Contest $contest)
    {
        return $this->getAllDates($contest, new \DateTime());
    }

    /**
     * @param Participation $participation
     *
     * @return DataEntry[]
     */
    public function getEditableEntries(Participation $participation)
    {
        $dates = $this->getAllPastDates($participation->getContest());
        $existingEntries = $this->getDataEntryRepository()->findBy(array('participation' => $participation->getId()));

        $indexedEntries = array();
        /** @var $entry DataEntry */
        foreach ($existingEntries as $entry) {
            $indexedEntries[$this->getFormKey($entry->getDate())] = $entry;
        }

        $entries = array();
        foreach ($dates as $date) {
            $key = $this->getFormKey($date);
            $entries[$key] = isset($indexedEntries[$key]) ? $indexedEntries[$key] : $this->createEntry($participation, $date);
        }

        return $entries;
    }

    private function createEntry(Participation $participation, \DateTime $date)
    {
        $entry = new DataEntry();

        $entry->setParticipation($participation)
            ->setDate($date);

        return $entry;
    }

    private function getFormKey(\DateTime $date)
    {
        return sprintf('date_%s', $date->format('U'));
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\DataEntryRepository
     */
    private function getDataEntryRepository()
    {
        return $this->registry->getRepository('WebBundle:DataEntry');
    }

    private function getFirstDate(Contest $contest)
    {
        $date = clone $contest->getStartDate();

        switch ($contest->getGranularity()) {
            case Contest::GRANULARITY_DAY:
                return $date;

            case Contest::GRANULARITY_WEEK:
                // Find the last monday.
                // We start by adding 1 day so that a monday finds itself, not the previous one.
                return $date->modify('+1 day')->modify('last monday');

            case Contest::GRANULARITY_MONTH:
                // Find the first day of the month.
                return $date->modify('first day of this month');

            default:
                throw new \LogicException(sprintf('Unsupported granularity "%s"', $contest->getGranularity()));
        }
    }
}
