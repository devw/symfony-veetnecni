<?php

namespace Incenteev\WebBundle\Comment\Acl;

use FOS\CommentBundle\Acl\ThreadAclInterface;
use FOS\CommentBundle\Model\ThreadInterface;
use Incenteev\WebBundle\Comment\CommentUtils;
use Symfony\Component\Security\Core\SecurityContextInterface;

class ThreadAcl implements ThreadAclInterface
{
    private $commentUtils;
    private $securityContext;

    /**
     * Constructor.
     *
     * @param CommentUtils             $commentUtils
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(CommentUtils $commentUtils, SecurityContextInterface $securityContext)
    {
        $this->commentUtils = $commentUtils;
        $this->securityContext = $securityContext;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Thread.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * Checks if the Security token is allowed to view the specified Thread.
     *
     * @param ThreadInterface $thread
     *
     * @return boolean
     */
    public function canView(ThreadInterface $thread)
    {
        $object = $this->commentUtils->findContestForThread($thread);

        if (null === $object) {
            return true;
        }

        $token = $this->securityContext->getToken();
        if (null === $token) {
            return false;
        }
        $user = $token->getUser();

        return $object->getOrganization() === $user->getOrganization();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Thread.
     *
     * @param ThreadInterface $thread
     *
     * @return boolean
     */
    public function canEdit(ThreadInterface $thread)
    {
        $object = $this->commentUtils->findContestForThread($thread);

        if (null === $object) {
            return true;
        }

        return false;
    }

    /**
     * Checks if the Security token is allowed to delete a specific Thread.
     *
     * @param ThreadInterface $thread
     *
     * @return boolean
     */
    public function canDelete(ThreadInterface $thread)
    {
        return false;
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param ThreadInterface $thread
     */
    public function setDefaultAcl(ThreadInterface $thread)
    {
    }

    /**
     * Role based Acl does not require setup.
     */
    public function installFallbackAcl()
    {
    }

    /**
     * Role based Acl does not require setup.
     */
    public function uninstallFallbackAcl()
    {
    }
}
