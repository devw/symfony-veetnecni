<?php

namespace Incenteev\WebBundle\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Incenteev\WebBundle\Entity\Contest;

/**
 * CommentRepository
 */
class CommentRepository extends EntityRepository
{
    /**
     * @param Contest $contest
     * @param integer $limit
     *
     * @return \Incenteev\WebBundle\Entity\Comment[]
     */
    public function getTopMessages(Contest $contest, $limit = 5)
    {
        $dql = <<<DQL
SELECT c, a
FROM WebBundle:Comment c
LEFt JOIN c.author a
WHERE c.ancestors = ''
AND IDENTITY(c.thread) = :thread
AND c.createdAt >= :date
ORDER BY c.createdAt DESC
DQL;

        $q = $this->getEntityManager()->createQuery($dql)
            ->setMaxResults($limit)
            ->setParameter('thread', 'contest-' . $contest->getId())
            // This needs to be changed to allow getting the messages since the last planned digest
            ->setParameter('date', new \DateTime('-7days'));

        return $q->getResult();
    }
}
