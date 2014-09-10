<?php

namespace Incenteev\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends Controller
{
    public function indexAction()
    {
        /** @var $user \Incenteev\WebBundle\Entity\User */
        $user = $this->getUser();

        $users = $this->getUserRepository()->getMembersQueryBuilder($user->getOrganization()->getId())->getQuery()->getArrayResult();

        return new JsonResponse(array(
            'users' => $users,
        ));
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\UserRepository
     */
    private function getUserRepository()
    {
        return $this->getDoctrine()->getRepository('WebBundle:User');
    }
}
