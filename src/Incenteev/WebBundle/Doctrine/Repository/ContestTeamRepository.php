<?php

namespace Incenteev\WebBundle\Doctrine\Repository;

use Incenteev\WebBundle\Entity\User;
use Incenteev\WebBundle\Entity\Contest;
use Doctrine\ORM\EntityRepository;

/**
 * ContestTeamRepository
 */
class ContestTeamRepository extends EntityRepository
{
    /**
     * @param Contest $contest
     *
     * @return \Incenteev\WebBundle\Entity\ContestTeam[]
     */
    public function getContestTeamsWithParticipants(Contest $contest)
    {
        $qb = $this->createQueryBuilder('c')
            ->addSelect('p', 'u')
            ->leftJoin('c.participations', 'p')
            ->leftJoin('p.user', 'u')
            ->where('IDENTITY(c.contest) = :contest')
            ->setParameter('contest', $contest->getId());

        return $qb->getQuery()->getResult();
    }

    /**
     * Gets all teams of a contest with their score.
     *
     * @param Contest $contest
     *
     * @return array
     */
    public function getContestBoard(Contest $contest)
    {
        $dql = <<<DQL
SELECT t AS team, IFNULL(SUM(d.value), 0) AS score
FROM WebBundle:ContestTeam t
LEFT JOIN t.participations p
LEFT JOIN WebBundle:DataEntry d WITH d.participation = p
WHERE IDENTITY(t.contest) = :contest
GROUP BY t
ORDER BY score DESC, t.name ASC
DQL;

        $q = $this->getEntityManager()->createQuery($dql)
            ->setParameter('contest', $contest->getId());

        return $q->getResult();
    }
}
