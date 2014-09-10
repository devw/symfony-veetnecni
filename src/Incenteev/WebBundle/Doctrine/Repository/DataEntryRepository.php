<?php

namespace Incenteev\WebBundle\Doctrine\Repository;

use Incenteev\WebBundle\Entity\Contest;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityRepository;

/**
 * DataEntryRepository
 */
class DataEntryRepository extends EntityRepository
{
    /**
     * @param Contest $contest
     *
     * @return \Incenteev\WebBundle\Entity\DataEntry[]
     */
    public function getAllEntries(Contest $contest)
    {
        $qb = $this->createQueryBuilder('d')
            ->addSelect('p', 'u')
            ->join('d.participation', 'p')
            ->join('p.user', 'u')
            ->where('p.contest = :contest')
            ->setParameter('contest', $contest);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param \DateTime[] $dates
     * @param array       $participations
     *
     * @return \Incenteev\WebBundle\Entity\DataEntry[]
     */
    public function findByDatesAndParticipations(array $dates, array $participations)
    {
        $formattedDates = array();

        $platform = $this->getEntityManager()->getConnection()->getDatabasePlatform();
        $dateType = Type::getType('date');

        foreach ($dates as $date) {
            $formattedDates[] = $dateType->convertToDatabaseValue($date, $platform);
        }

        return $this->findBy(array('date' => $formattedDates, 'participation' => $participations));
    }
}
