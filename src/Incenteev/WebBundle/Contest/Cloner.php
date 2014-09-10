<?php

namespace Incenteev\WebBundle\Contest;

use Doctrine\Common\Persistence\ManagerRegistry;
use Gaufrette\Filesystem;
use Incenteev\WebBundle\Avatar\AvatarEnabledInterface;
use Incenteev\WebBundle\Avatar\BackgroundEnabledInterface;
use Incenteev\WebBundle\Avatar\PathResolverInterface;
use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Entity\ContestTeam;
use Incenteev\WebBundle\Entity\Participation;
use Incenteev\WebBundle\Entity\Periodicity;
use Incenteev\WebBundle\Entity\Prize;

class Cloner
{
    private $invitationManager;
    private $registry;
    private $pathResolver;
    private $filesystem;

    public function __construct(ManagerRegistry $registry, InvitationManager $invitationManager, PathResolverInterface $pathResolver, Filesystem $filesystem)
    {
        $this->registry = $registry;
        $this->invitationManager = $invitationManager;
        $this->pathResolver = $pathResolver;
        $this->filesystem = $filesystem;
    }

    public function cloneContest(Contest $contest)
    {
        $em = $this->registry->getManager();

        $newContest = new Contest();

        $newContest->setName($contest->getName())
            ->setOrganization($contest->getOrganization())
            ->setPublished(false)
            ->setDescription($contest->getDescription())
            ->setRules($contest->getRules())
            ->setStartDate($contest->getStartDate())
            ->setEndDate($contest->getEndDate())
            ->setInvitationText($contest->getInvitationText())
            ->setReminderText($contest->getReminderText())
            ->setGranularity($contest->getGranularity())
            ->setDataName($contest->getDataName())
            ->setUnit($contest->getUnit())
            ->setUpdatedByParticipants($contest->isUpdatedByParticipants())
            ->setTheme($contest->getTheme());

        foreach ($contest->getOwners() as $owner) {
            $newContest->addOwner($owner);
        }

        $em->persist($newContest);

        if (null !== $contest->getReminderPeriodicity()) {
            $reminderPeriodicity = $this->copyPeriodicity($contest->getReminderPeriodicity());

            $newContest->setReminderPeriodicity($reminderPeriodicity);
            $em->persist($reminderPeriodicity);
        }

        if (null !== $contest->getSummaryPeriodicity()) {
            $summaryPeriodicity = $this->copyPeriodicity($contest->getSummaryPeriodicity());

            $newContest->setSummaryPeriodicity($summaryPeriodicity);
            $em->persist($summaryPeriodicity);
        }

        $this->copyAvatar($contest, $newContest);
        $this->copyBackground($contest, $newContest);

        /** @var $prize Prize */
        foreach ($contest->getPrizes() as $prize) {
            $newPrize = new Prize();

            $newPrize->setContest($newContest)
                ->setName($prize->getName())
                ->setDescription($prize->getDescription())
                ->setRank($prize->getRank());

            $this->copyAvatar($prize, $newPrize);
            $em->persist($prize);
        }

        $clonedContestTeams = array();

        /** @var $participation Participation */
        foreach ($contest->getParticipations() as $participation) {
            // TODO handle the sender: people cloning or sender in the cloned contest ?
            $newParticipation = $this->invitationManager->createParticipation($participation->getUser(), $newContest);

            $team = $participation->getContestTeam();

            if (null !== $team) {
                $teamId = $team->getId();

                if (!isset($clonedContestTeams[$teamId])) {
                    $newTeam = new ContestTeam();

                    $newTeam->setContest($newContest)
                        ->setName($team->getName());

                    $this->copyAvatar($team, $newTeam);
                    $em->persist($newTeam);

                    $clonedContestTeams[$teamId] = $newTeam;
                }

                $newParticipation->setContestTeam($clonedContestTeams[$teamId]);
            }

            $em->persist($newParticipation);
        }

        return $newContest;
    }

    private function copyPeriodicity(Periodicity $source)
    {
        $target = new Periodicity();

        $target->setDays($source->getDays())
            ->setHours($source->getHours())
            ->setGuessed($source->isGuessed());

        return $target;
    }

    private function copyAvatar(AvatarEnabledInterface $source, AvatarEnabledInterface $target)
    {
        $sourceAvatar = $source->getAvatarPath();

        if (null === $sourceAvatar) {
            $target->setAvatarPath(null);

            return;
        }

        $sourcePath = $this->pathResolver->getPath($source, PathResolverInterface::TYPE_AVATAR);

        if (!$this->filesystem->has($sourcePath)) {
            $target->setAvatarPath(null);

            return;
        }

        $target->setAvatarPath(md5(uniqid(mt_rand(), true)).'.png');

        $this->copyUploadedImage(
            $sourcePath,
            $this->pathResolver->getPath($target, PathResolverInterface::TYPE_AVATAR)
        );
    }

    private function copyBackground(BackgroundEnabledInterface $source, BackgroundEnabledInterface $target)
    {
        $sourceBackground = $source->getBackgroundPath();

        if (empty($sourceBackground) || 0 === strpos($sourceBackground, 'background/')) {
            $target->setBackgroundPath($sourceBackground);

            return;
        }

        $sourcePath = $this->pathResolver->getPath($source, PathResolverInterface::TYPE_BACKGROUND);

        if (!$this->filesystem->has($sourcePath)) {
            $target->setBackgroundPath(null);

            return;
        }

        $target->setBackgroundPath(md5(uniqid(mt_rand(), true)).'.png');

        $this->copyUploadedImage(
            $sourcePath,
            $this->pathResolver->getPath($target, PathResolverInterface::TYPE_BACKGROUND)
        );
    }

    private function copyUploadedImage($oldPath, $newPath)
    {
        $this->filesystem->write($newPath, $this->filesystem->read($oldPath), true, array('contentType' => 'image/png'));
    }
}
