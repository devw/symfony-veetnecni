<?php

namespace Incenteev\WebBundle\Doctrine\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Entity\User;

/**
 * ContestRepository
 */
class ContestRepository extends EntityRepository
{
    /**
     * @param integer $id
     *
     * @return \Incenteev\WebBundle\Entity\Contest|null
     */
    public function getContestWithParticipants($id)
    {
        $qb = $this->getContestsWithParticipantsQueryBuilder();

        $qb->where('c.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param integer $id
     *
     * @return \Incenteev\WebBundle\Entity\Contest|null
     */
    public function getContestWithOwners($id)
    {
        $qb = $this->createQueryBuilder('c')
            ->addSelect('o')
            ->leftJoin('c.owners', 'o')
            ->where('c.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @param integer $id
     *
     * @return \Incenteev\WebBundle\Entity\Contest|null
     */
    public function getFullContest($id)
    {
        $qb = $this->createQueryBuilder('c')
            ->addSelect('p', 'u', 'ct', 'pr', 'rp', 'sp', 'o')
            ->leftJoin('c.participations', 'p')
            ->leftJoin('p.user', 'u')
            ->leftJoin('p.contestTeam', 'ct')
            ->leftJoin('c.prizes', 'pr')
            ->leftJoin('c.reminderPeriodicity', 'rp')
            ->leftJoin('c.summaryPeriodicity', 'sp')
            ->leftJoin('c.owners', 'o')
            ->where('c.id = :id')
            ->setParameter('id', $id);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return \Incenteev\WebBundle\Entity\Contest[]
     */
    public function getCurrentContests()
    {
        $qb = $this->getCurrentContestsBuilder();

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $week
     * @param int $day
     * @param int $hour
     *
     * @return \Incenteev\WebBundle\Entity\Contest[]
     */
    public function getRemindedContests($week, $day, $hour)
    {
        $qb = $this->getCurrentContestsBuilder();

        $qb->join('c.reminderPeriodicity', 'r')
            ->andWhere('BIT_AND(r.weeks, :week) > 0')
            ->andWhere('r.days = 0 OR BIT_AND(r.days, :day) > 0')
            ->andWhere('BIT_AND(r.hours, :hour) > 0')
            ->andWhere('c.updatedByParticipants = :updatedByParticipants')
            ->setParameter('week', $week)
            ->setParameter('day', $day)
            ->setParameter('hour', $hour)
            ->setParameter('updatedByParticipants', true);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param int $week
     * @param int $day
     * @param int $hour
     *
     * @return \Incenteev\WebBundle\Entity\Contest[]
     */
    public function getSummaryContests($week, $day, $hour)
    {
        $qb = $this->getContestsWithParticipantsQueryBuilder();

        $qb->addSelect('o', 'org')
            ->leftJoin('c.summaryPeriodicity', 'sp')
            ->leftJoin('c.organization', 'org')
            ->leftJoin('c.owners', 'o')
            ->andWhere('c.published = :published')
            ->andWhere('c.startDate <= :date')
            ->andWhere('c.endDate >= :date')
            ->andWhere('BIT_AND(sp.weeks, :week) > 0')
            ->andWhere('BIT_AND(sp.days, :day) > 0')
            ->andWhere('BIT_AND(sp.hours, :hour) > 0')
            ->setParameter('week', $week)
            ->setParameter('day', $day)
            ->setParameter('hour', $hour)
            ->setParameter('date', new \DateTime())
            ->setParameter('published', true);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param User $user
     *
     * @return \Incenteev\WebBundle\Entity\Contest[]
     */
    public function getUserContests(User $user)
    {
        $qb = $this->createQueryBuilder('c')
            ->andWhere('c.organization = :organization')
            ->andWhere('c.published = :published OR :user MEMBER OF c.owners')
            ->addOrderBy('c.endDate', 'DESC')
            ->setParameter('organization', $user->getOrganization())
            ->setParameter('user', $user)
            ->setParameter('published', true);

        return $qb->getQuery()->getResult();
    }

    /**
     * Removes a contest and all its related data.
     *
     * @param Contest $contest
     */
    public function deleteContest(Contest $contest)
    {
        $queries = array();

        $queries[] = <<<DQL
DELETE FROM WebBundle:DataEntry d
WHERE IDENTITY(d.participation) IN (
    SELECT p
    FROM WebBundle:Participation p
    WHERE IDENTITY(p.contest) = :contest
)
DQL;
        $queries[] = <<<DQL
DELETE FROM WebBundle:Participation p
WHERE IDENTITY(p.contest) = :contest
DQL;

        $queries[] = <<<DQL
DELETE FROM WebBundle:ContestTeam c
WHERE IDENTITY(c.contest) = :contest
DQL;

        $queries[] = <<<DQL
DELETE FROM WebBundle:Prize p
WHERE IDENTITY(p.contest) = :contest
DQL;

        $remover = function (EntityManager $em) use ($queries, $contest) {
            foreach ($queries as $dql) {
                $em->createQuery($dql)
                    ->setParameter('contest', $contest->getId())
                    ->execute();
            }

            $em->remove($contest);
        };

        $this->getEntityManager()->transactional($remover);
    }

    private function getContestsWithParticipantsQueryBuilder()
    {
        $qb = $this->createQueryBuilder('c')
            ->addSelect('p', 'u')
            ->leftJoin('c.participations', 'p')
            ->leftJoin('p.user', 'u');

        return $qb;
    }

    private function getCurrentContestsBuilder()
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.startDate <= :date')
            ->andWhere('c.endDate >= :date')
            ->andWhere('c.published = :published')
            ->setParameter('date', new \DateTime())
            ->setParameter('published', true);

        return $qb;
    }
}
