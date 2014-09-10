<?php

namespace Incenteev\WebBundle\Comment;

use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\ThreadInterface;

class CommentUtils
{
    private $registry;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param \FOS\CommentBundle\Model\CommentInterface $comment
     *
     * @return \Incenteev\WebBundle\Entity\Contest|null
     */
    public function findContestForComment(CommentInterface $comment)
    {
        return $this->findContestForThread($comment->getThread());
    }

    /**
     * @param \FOS\CommentBundle\Model\ThreadInterface $thread
     *
     * @return \Incenteev\WebBundle\Entity\Contest|null
     */
    public function findContestForThread(ThreadInterface $thread)
    {
        if (0 === strpos($thread->getId(), 'contest-')) {
            return $this->getContestRepository()->getContestWithOwners(substr($thread->getId(), 8));
        }

        return null;
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\ContestRepository
     */
    private function getContestRepository()
    {
        return $this->registry->getRepository('WebBundle:Contest');
    }
}
