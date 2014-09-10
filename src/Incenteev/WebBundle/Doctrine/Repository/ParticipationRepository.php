<?php

namespace Incenteev\WebBundle\Doctrine\Repository;

use Incenteev\WebBundle\Entity\Contest;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

/**
 * ParticipationRepository
 */
class ParticipationRepository extends EntityRepository
{
    /**
     * @param Contest $contest
     *
     * @return \Incenteev\WebBundle\Entity\Participation[]
     */
    public function getContestParticipations(Contest $contest)
    {
        return $this->getContestParticipationsQueryBuilder($contest->getId())->getQuery()->getResult();
    }

    public function getContestParticipationsQueryBuilder($contestId)
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('u')
            ->join('p.user', 'u')
            ->where('IDENTITY(p.contest) = :contest')
            ->addOrderBy('u.lastName', 'ASC')
            ->addOrderBy('u.firstName', 'ASC')
            ->setParameter('contest', $contestId);

        return $qb;
    }

    /**
     * Gets all participation to a contest with their score.
     *
     * @param Contest $contest
     *
     * @return array
     */
    public function getContestBoard(Contest $contest, $limit = null, $offset = null)
    {
        $dql = <<<DQL
SELECT p AS participation, u, IFNULL(SUM(d.value), 0) AS score
FROM WebBundle:Participation p
JOIN p.user u
LEFT JOIN WebBundle:DataEntry d WITH d.participation = p
WHERE IDENTITY(p.contest) = :contest
GROUP BY p
ORDER BY score DESC, u.lastName ASC, u.firstName ASC
DQL;

        $q = $this->getEntityManager()->createQuery($dql)
            ->setParameter('contest', $contest->getId())
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return $q->getResult();
    }

    /**
     * Gets all participation to a contest without data for a given date.
     *
     * @param Contest   $contest
     * @param \DateTime $date
     *
     * @return \Incenteev\WebBundle\Entity\Participation[]
     */
    public function getParticipationWithoutData(Contest $contest, \DateTime $date)
    {
        $dql = <<<DQL
SELECT p, u
FROM WebBundle:Participation p
JOIN p.user u
WHERE IDENTITY(p.contest) = :contest
AND NOT EXISTS(
    SELECT d.id
    FROM WebBundle:DataEntry d
    WHERE d.participation = p
    AND d.date = :date
)
DQL;

        $q = $this->getEntityManager()->createQuery($dql)
            ->setParameter('contest', $contest->getId())
            ->setParameter('date', $date);

        return $q->getResult();
    }

    /**
     * Gets all participation to a contest with missing data.
     *
     * @param Contest $contest
     * @param int     $count   the number of data a participant should have
     *
     * @return \Incenteev\WebBundle\Entity\Participation[]
     */
    public function getParticipationWithMissingData(Contest $contest, $count)
    {
        $dql = <<<DQL
SELECT p, u
FROM WebBundle:Participation p
JOIN p.user u
LEFT JOIN WebBundle:DataEntry d WITH d.participation = p
WHERE IDENTITY(p.contest) = :contest
GROUP BY p
HAVING COUNT(d) != :count
DQL;

        $q = $this->getEntityManager()->createQuery($dql)
            ->setParameter('contest', $contest->getId())
            ->setParameter('count', $count);

        return $q->getResult();
    }

    /**
     * @param Contest|null $contest
     *
     * @return \Incenteev\WebBundle\Entity\Participation[]
     */
    public function getPendingParticipations(Contest $contest = null)
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('u')
            ->join('p.user', 'u')
            ->where('p.accepted = :accepted')
            ->setParameter('accepted', false);

        if (null !== $contest) {
            $qb->andWhere('p.contest = :contest')
                ->setParameter('contest', $contest);
        }

        return $qb->getQuery()->getResult();
    }

    public function deleteUserParticipations(Contest $contest, array $userIds)
    {
        $queries = array();

        $queries[] = <<<DQL
DELETE FROM WebBundle:DataEntry d
WHERE IDENTITY(d.participation) IN (
    SELECT p
    FROM WebBundle:Participation p
    WHERE IDENTITY(p.contest) = :contest
    AND IDENTITY(p.user) IN (:users)
)
DQL;
        $queries[] = <<<DQL
DELETE FROM WebBundle:Participation p
WHERE IDENTITY(p.contest) = :contest
AND IDENTITY(p.user) IN (:users)
DQL;

        $this->getEntityManager()->transactional(function (EntityManager $em) use ($queries, $contest, $userIds) {
            foreach ($queries as $dql) {
                $em->createQuery($dql)
                    ->setParameter('contest', $contest->getId())
                    ->setParameter('users', $userIds)
                    ->execute();
            }
        });
    }

    /**
     * @param string $token
     * @param int    $contestId
     *
     * @return \Incenteev\WebBundle\Entity\Participation|null
     */
    public function getParticipationByToken($token, $contestId)
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('u', 'c')
            ->join('p.user', 'u')
            ->join('p.contest', 'c')
            ->where('IDENTITY(p.contest) = :contest')
            ->andWhere('p.token = :token')
            ->setParameter('contest', $contestId)
            ->setParameter('token', $token);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
