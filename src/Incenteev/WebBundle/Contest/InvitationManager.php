<?php

namespace Incenteev\WebBundle\Contest;

use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Incenteev\WebBundle\Entity\Participation;
use Incenteev\WebBundle\Form\Model\ContestInvitation;
use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Entity\User;
use Incenteev\WebBundle\Mailer\MailerInterface;
use Incenteev\WebBundle\Util\NameGuesserInterface;

class InvitationManager
{
    private $mailer;
    private $nameGuesser;
    private $registry;
    private $tokenGenerator;

    public function __construct(ManagerRegistry $registry, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, NameGuesserInterface $nameGuesser)
    {
        $this->registry = $registry;
        $this->tokenGenerator = $tokenGenerator;
        $this->mailer = $mailer;
        $this->nameGuesser = $nameGuesser;
    }

    public function sendInvitations(Contest $contest)
    {
        $participations = $this->getParticipationRepository()->getPendingParticipations($contest);

        $this->sendInvitationMails($participations);
    }

    public function processInvitations(Contest $contest, ContestInvitation $invitations, User $sender)
    {
        $this->filterExistingUsers($contest, $invitations);

        $participants = $this->getUserRepository()->getParticipants($contest);
        $participations = array();

        foreach ($invitations->existingUsers as $user) {
            if (!in_array($user, $participants, true)) {
                $participations[] = $this->createParticipation($user, $contest, $sender);
            }
        }

        foreach ($invitations->invitedEmails as $email) {
            $user = $this->createUser($email, $contest, $sender);
            $participations[] = $this->createParticipation($user, $contest, $sender);
        }

        $this->registry->getManager()->flush();

        if ($contest->isPublished()) {
            $this->sendInvitationMails($participations);
        }
    }

    /**
     * @param \Incenteev\WebBundle\Entity\Participation[] $participations
     */
    private function sendInvitationMails(array $participations)
    {
        foreach ($participations as $participation) {
            $this->mailer->send($participation->getUser(), 'WebBundle:Mail:invitation.html.twig', array('participation' => $participation));
        }
    }

    private function filterExistingUsers(Contest $contest, ContestInvitation $invitations)
    {
        $invitations->invitedEmails = array_filter($invitations->invitedEmails);

        if (empty($invitations->invitedEmails)) {
            return;
        }

        $invitedEmails = array();

        foreach ($invitations->invitedEmails as $email) {
            $invitedEmails[] = mb_strtolower($email, 'UTF-8');
        }

        $knownEmailUsers = $this->getUserRepository()->getMembersByEmails($contest->getOrganization(), $invitedEmails);
        $removedEmails = array();

        foreach ($knownEmailUsers as $user) {
            if (!$invitations->existingUsers->contains($user)) {
                $invitations->existingUsers[] = $user;
            }
            $removedEmails[] = $user->getEmailCanonical();
        }

        $invitations->invitedEmails = array_unique(array_diff($invitedEmails, $removedEmails));
    }

    public function createParticipation(User $user, Contest $contest, User $sender = null)
    {
        $participation = new Participation();

        $participation->setContest($contest)
            ->setUser($user)
            ->setInvitedBy($sender)
            ->setToken($this->tokenGenerator->generateToken());

        $this->registry->getManager()->persist($participation);

        return $participation;
    }

    private function createUser($email, Contest $contest, User $creator)
    {
        $user = new User();
        $user->setOrganization($contest->getOrganization())
            ->setCreatedBy($creator)
            ->setEmail($email)
            ->setEnabled(false)
            ->setPassword('');

        $this->nameGuesser->guessNames($user);
        $this->registry->getManager()->persist($user);

        return $user;
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\UserRepository
     */
    private function getUserRepository()
    {
        return $this->registry->getRepository('WebBundle:User');
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\ParticipationRepository
     */
    private function getParticipationRepository()
    {
        return $this->registry->getRepository('WebBundle:Participation');
    }
}
