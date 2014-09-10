<?php

namespace Incenteev\WebBundle\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Entity\Organization;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{
    /**
     * Gets all user who participates to a contest
     *
     * @param Contest $contest
     *
     * @return \Incenteev\WebBundle\Entity\User[]
     */
    public function getParticipants(Contest $contest)
    {
        return $this->getParticipantsQuery($contest)->getResult();
    }

    /**
     * Gets all user who participates to a contest
     *
     * @param Contest $contest
     *
     * @return \Doctrine\ORM\Query
     */
    public function getParticipantsQuery(Contest $contest)
    {
        $dql = <<<DQL
SELECT u
FROM WebBundle:User u
JOIN WebBundle:Participation p WITH p.user = u
WHERE IDENTITY(p.contest) = :contest
ORDER BY u.lastName ASC, u.firstName ASC
DQL;

        $q = $this->getEntityManager()->createQuery($dql)
            ->setParameter('contest', $contest->getId());

        return $q;
    }

    public function getMembersQueryBuilder($organizationId)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('IDENTITY(u.organization) = :organization')
            ->setParameter('organization', $organizationId)
            ->addOrderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC');
    }

    public function getNonParticipantMembersQueryBuilder($organizationId, $contestId)
    {
        $condition = <<<DQL
NOT EXISTS(
    SELECT p.id
    FROM WebBundle:Participation p
    WHERE p.user = u
    AND IDENTITY(p.contest) = :contest
)
DQL;

        return $this->createQueryBuilder('u')
            ->andWhere('IDENTITY(u.organization) = :organization')
            ->andWhere($condition)
            ->setParameter('organization', $organizationId)
            ->setParameter('contest', $contestId);
    }

    /**
     * @param Organization $organization
     * @param array        $emails
     *
     * @return \Incenteev\WebBundle\Entity\User[]
     */
    public function getMembersByEmails(Organization $organization, array $emails)
    {
        return $this->findBy(array('organization' => $organization->getId(), 'emailCanonical' => $emails));
    }

    /**
     * Returns the emails used in the organization among the provided ones.
     *
     * @param array        $emails
     * @param Organization $organization
     *
     * @return array
     */
    public function getOrganizationEmails(array $emails, Organization $organization)
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('IDENTITY(u.organization) = :organization')
            ->setParameter('organization', $organization->getId());

        return $this->getEmails($qb, $emails);
    }

    /**
     * Returns the emails already used in other organizations among the provided ones.
     *
     * @param array        $emails
     * @param Organization $organization
     *
     * @return array
     */
    public function getEmailAlreadyUsed(array $emails, Organization $organization)
    {
        $qb = $this->createQueryBuilder('u')
            ->andWhere('IDENTITY(u.organization) != :organization')
            ->setParameter('organization', $organization->getId());

        return $this->getEmails($qb, $emails);
    }

    private function getEmails(QueryBuilder $qb, array $emails)
    {
        $qb->select('u.emailCanonical AS email')
            ->andWhere('u.emailCanonical IN(:emails)')
            ->setParameter('emails', $emails);

        $results = $qb->getQuery()->getScalarResult();

        return array_map(function ($v) { return $v['email']; }, $results);
    }
}
