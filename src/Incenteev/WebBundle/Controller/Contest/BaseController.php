<?php

namespace Incenteev\WebBundle\Controller\Contest;

use Incenteev\WebBundle\Entity\Contest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Contest base controller.
 *
 */
abstract class BaseController extends Controller
{
    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected function getDispatcher()
    {
        return $this->container->get('event_dispatcher');
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\ContestRepository
     */
    protected function getContestRepository()
    {
        return $this->getDoctrine()->getRepository('WebBundle:Contest');
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\ContestTeamRepository
     */
    protected function getContestTeamRepository()
    {
        return $this->getDoctrine()->getRepository('WebBundle:ContestTeam');
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\ParticipationRepository
     */
    protected function getParticipationRepository()
    {
        return $this->getDoctrine()->getRepository('WebBundle:Participation');
    }

    /**
     * @param int    $id
     * @param string $deniedMessage
     *
     * @return \Incenteev\WebBundle\Entity\Contest
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    protected function findContestForOwner($id, $deniedMessage = null)
    {
        /** @var $contest \Incenteev\WebBundle\Entity\Contest */
        $contest = $this->getContestRepository()->find($id);

        if (!$contest) {
            throw new NotFoundHttpException(sprintf('Unable to find contest with id %s.', $id));
        }

        $this->checkContestOwnerShip($contest, $deniedMessage);

        return $contest;
    }

    protected function checkContestOwnerShip(Contest $contest, $deniedMessage = null)
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        if (!$contest->hasOwner($user)) {
            throw new AccessDeniedException($deniedMessage ?: 'Only contests owners can edit a contest.');
        }
    }
}
