<?php

namespace Incenteev\WebBundle\Contest;

use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Entity\Periodicity;

class PeriodicityGuesser
{
    public function updateGuessedPeriodicity(Contest $contest)
    {
        $reminderPeriodicity = $contest->getReminderPeriodicity();
        $summaryPeriodicity = $contest->getSummaryPeriodicity();

        if (null !== $reminderPeriodicity && $reminderPeriodicity->isGuessed()) {
            $this->guessReminderPeriodicity($contest, $reminderPeriodicity);
        }

        if (null !== $summaryPeriodicity && $summaryPeriodicity->isGuessed()) {
            $this->guessSummaryPeriodicity($contest, $summaryPeriodicity);
        }
    }

    public function getGuessedReminderPeriodicity(Contest $contest)
    {
        $periodicity = new Periodicity();

        $periodicity->setGuessed(true);
        $this->guessReminderPeriodicity($contest, $periodicity);

        return $periodicity;
    }

    public function getGuessedSummaryPeriodicity(Contest $contest)
    {
        $periodicity = new Periodicity();

        $periodicity->setGuessed(true);
        $this->guessSummaryPeriodicity($contest, $periodicity);

        return $periodicity;
    }

    private function guessReminderPeriodicity(Contest $contest, Periodicity $periodicity)
    {
        $periodicity->setGuessed(true)
            ->setWeeks(Periodicity::ALL_WEEKS)
            ->setHours(Periodicity::HOUR_17);

        switch ($contest->getGranularity()) {
            case Contest::GRANULARITY_DAY:
                $periodicity->setDays(Periodicity::WEEK_DAYS);

                return;

            case Contest::GRANULARITY_WEEK:
                $periodicity->setDays(Periodicity::DAY_MONDAY | Periodicity::DAY_WEDNESDAY | Periodicity::DAY_FRIDAY);

                return;

            case Contest::GRANULARITY_MONTH:
                $periodicity->setDays(Periodicity::DAY_FRIDAY);

                return;
        }
    }

    private function guessSummaryPeriodicity(Contest $contest, Periodicity $periodicity)
    {
        $periodicity->setGuessed(true)
            ->setWeeks(Periodicity::ALL_WEEKS)
            ->setHours(Periodicity::HOUR_10);

        switch ($contest->getGranularity()) {
            case Contest::GRANULARITY_DAY:
                $periodicity->setDays(Periodicity::WEEK_DAYS);

                return;

            case Contest::GRANULARITY_WEEK:
                $periodicity->setDays(Periodicity::DAY_TUESDAY | Periodicity::DAY_THURSDAY);

                return;

            case Contest::GRANULARITY_MONTH:
                $periodicity->setDays(Periodicity::DAY_MONDAY);

                return;
        }
    }
}
