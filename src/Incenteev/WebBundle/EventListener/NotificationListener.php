<?php

namespace Incenteev\WebBundle\EventListener;

use FOS\CommentBundle\Events as FOSCommentEvents;
use FOS\CommentBundle\Event\CommentPersistEvent;
use FOS\CommentBundle\Model\CommentManagerInterface;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Incenteev\WebBundle\Comment\CommentUtils;
use Incenteev\WebBundle\IncenteevEvents;
use Incenteev\WebBundle\Entity\User;
use Incenteev\WebBundle\Event\ContestEvent;
use Incenteev\WebBundle\Notification\NotificationManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listener responsible to send notifications
 */
class NotificationListener implements EventSubscriberInterface
{
    private $commentManager;
    private $commentUtils;
    private $notificationManager;

    public function __construct(NotificationManager $notificationManager, CommentManagerInterface $commentManager, CommentUtils $commentUtils)
    {
        $this->notificationManager = $notificationManager;
        $this->commentManager = $commentManager;
        $this->commentUtils = $commentUtils;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSCommentEvents::COMMENT_PRE_PERSIST => 'onCommentPrePersist',
            IncenteevEvents::CONTEST_PUBLISH_SUCCESS => 'onContestPublished',
        );
    }

    public function onCommentPrePersist(CommentPersistEvent $event)
    {
        $comment = $event->getComment();

        if (null === $comment->getParent()) {
            return;
        }

        $contest = $this->commentUtils->findContestForComment($comment);

        if (null === $contest) {
            return;
        }

        if (!$this->commentManager->isNewComment($comment)) {
            return;
        }

        $notifiedUsers = new \SplObjectStorage();
        $author = null;

        if ($comment instanceof SignedCommentInterface) {
            $author = $comment->getAuthor();
        }

        $parent = $comment->getParent();

        if ($parent instanceof SignedCommentInterface && $parent->getAuthor() !== $author && $parent->getAuthor() !== null) {
            $notifiedUsers->attach($parent->getAuthor());
        }

        $replies = $this->commentManager->findCommentTreeByCommentId($parent->getId());

        foreach ($replies as $replyTree) {
            $reply = $replyTree['comment'];

            if ($reply instanceof SignedCommentInterface && $reply->getAuthor() !== $author && $reply->getAuthor() !== null) {
                $notifiedUsers->attach($reply->getAuthor());
            }
        }

        $this->notificationManager->send(
            NotificationManager::TYPE_COMMENT_REPLY,
            iterator_to_array($notifiedUsers),
            array('comment' => $comment, 'contest' => $contest)
        );
    }

    public function onContestPublished(ContestEvent $event)
    {
        $contest = $event->getContest();

        $this->notificationManager->send(
            NotificationManager::TYPE_CONTEST_PUBLISHED,
            $contest->getOwners()->toArray(),
            array('contest' => $contest)
        );
    }
}
