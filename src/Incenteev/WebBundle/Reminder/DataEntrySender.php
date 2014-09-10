<?php

namespace Incenteev\WebBundle\Reminder;

use Doctrine\Common\Persistence\ManagerRegistry;
use Incenteev\WebBundle\Contest\DataEntryManager;
use Incenteev\WebBundle\Entity\Periodicity;
use Incenteev\WebBundle\Mailer\MailerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class DataEntrySender implements SenderInterface
{
    private $dataEntryManager;
    private $mailer;
    private $registry;
    private $translator;

    public function __construct(ManagerRegistry $registry, MailerInterface $mailer, DataEntryManager $dataEntryManager, TranslatorInterface $translator)
    {
        $this->registry = $registry;
        $this->mailer = $mailer;
        $this->dataEntryManager = $dataEntryManager;
        $this->translator = $translator;
    }

    public function sendReminders()
    {
        $date = new \DateTime();

        $week = (int) ceil($date->format('d') / 7);
        $weekDay = (int) $date->format('w');
        $hour = (int) $date->format('G');

        $weekMask = 1 << ($week - 1);
        if (5 === $week) {
            $weekMask |= Periodicity::WEEK_4;
        }

        $contests = $this->getContestRepository()->getRemindedContests($weekMask, 1 << $weekDay, 1 << $hour);

        foreach ($contests as $contest) {
            $this->translator->setLocale($contest->getOrganization()->getLanguage());

            $dateCount = count($this->dataEntryManager->getAllPastDates($contest));
            $participations = $this->getParticipationRepository()->getParticipationWithMissingData($contest, $dateCount);

            foreach ($participations as $participation) {
                $this->mailer->send($participation->getUser(), 'WebBundle:Mail/Reminder:dataEntry.html.twig', array('participation' => $participation, 'date' => $date));
            }
        }
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\ContestRepository
     */
    private function getContestRepository()
    {
        return $this->registry->getRepository('WebBundle:Contest');
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\ParticipationRepository
     */
    private function getParticipationRepository()
    {
        return $this->registry->getRepository('WebBundle:Participation');
    }
}
