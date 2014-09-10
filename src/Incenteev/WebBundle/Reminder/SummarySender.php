<?php

namespace Incenteev\WebBundle\Reminder;

use Doctrine\Common\Persistence\ManagerRegistry;
use Incenteev\WebBundle\Contest\DataEntryManager;
use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Entity\Participation;
use Incenteev\WebBundle\Entity\Periodicity;
use Incenteev\WebBundle\Mailer\MailerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SummarySender implements SenderInterface
{
    private $dataEntryManager;
    private $mailer;
    private $registry;
    private $translator;

    public function __construct(MailerInterface $mailer, ManagerRegistry $registry, DataEntryManager $dataEntryManager, TranslatorInterface $translator)
    {
        $this->mailer = $mailer;
        $this->registry = $registry;
        $this->dataEntryManager = $dataEntryManager;
        $this->translator = $translator;
    }

    public function sendReminders()
    {
        // TODO remove code duplication between senders
        $date = new \DateTime();

        $week = (int) ceil($date->format('d') / 7);
        $weekDay = (int) $date->format('w');
        $hour = (int) $date->format('G');

        $weekMask = 1 << ($week - 1);
        if (5 === $week) {
            $weekMask |= Periodicity::WEEK_4;
        }

        $contests = $this->getContestRepository()->getSummaryContests($weekMask, 1 << $weekDay, 1 << $hour);

        $participationRepository = $this->getParticipationRepository();

        foreach ($contests as $contest) {
            $this->translator->setLocale($contest->getOrganization()->getLanguage());

            $board = $participationRepository->getContestBoard($contest, 10, 0);
            $dateCount = count($this->dataEntryManager->getAllPastDates($contest));

            $participations = $contest->getParticipations();
            $notUpdatedUsers = $participationRepository->getParticipationWithMissingData($contest, $dateCount);
            $pendingParticipations = $participations->filter(function (Participation $participation) {
                return !$participation->isAccepted();
            });
            $topMessages = $this->getCommentRepository()->getTopMessages($contest);

            $users = $participations->map(function (Participation $participation) {
                return $participation->getUser();
            });
            foreach ($contest->getOwners() as $owner) {
                if (!$users->contains($owner)) {
                    $users->add($owner);
                }
            }

            /** @var $users \Incenteev\WebBundle\Entity\User[] */

            foreach ($users as $user) {
                $this->mailer->send(
                    $user,
                    'WebBundle:Mail/Notification:summary.html.twig',
                    array(
                        'contest' => $contest,
                        'board' => $board,
                        'not_updated_users' => $notUpdatedUsers,
                        'pending_users' => $pendingParticipations,
                        'recipient' => $user,
                        'top_messages' => $topMessages
                    )
                );
            }
        }
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\CommentRepository
     */
    private function getCommentRepository()
    {
        return $this->registry->getRepository('WebBundle:Comment');
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
