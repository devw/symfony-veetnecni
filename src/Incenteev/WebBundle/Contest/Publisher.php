<?php

namespace Incenteev\WebBundle\Contest;

use Incenteev\WebBundle\IncenteevEvents;
use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Event\ContestEvent;
use Doctrine\Common\Persistence\ManagerRegistry;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Publisher
{
    private $dispatcher;
    private $invitationManager;
    private $registry;
    private $threadManager;

    public function __construct(ManagerRegistry $registry, EventDispatcherInterface $dispatcher, InvitationManager $invitationManager, ThreadManagerInterface $threadManager)
    {
        $this->registry = $registry;
        $this->dispatcher = $dispatcher;
        $this->invitationManager = $invitationManager;
        $this->threadManager = $threadManager;
    }

    public function publish(Contest $contest)
    {
        $this->invitationManager->sendInvitations($contest);

        $contest->setPublished(true);

        $em = $this->registry->getManager();

        $em->persist($contest);

        $thread = $this->threadManager->findThreadById(sprintf('contest-%s', $contest->getId()));
        $thread->setCommentable(true);
        $em->persist($thread);

        $this->dispatcher->dispatch(IncenteevEvents::CONTEST_PUBLISH_SUCCESS, new ContestEvent($contest));

        $em->flush();
    }
}
