<?php

namespace Incenteev\WebBundle\Comment\Acl;

use FOS\CommentBundle\Acl\CommentAclInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Incenteev\WebBundle\Comment\CommentUtils;
use Symfony\Component\Security\Core\SecurityContextInterface;

class CommentAcl implements CommentAclInterface
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
     * Checks if the Security token has an appropriate role to create a new Comment.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    /**
     * Checks if the Security token is allowed to view the specified Comment.
     *
     * Viewing the comments is allowed as soon as viewing the thread is allowed.
     *
     * @param CommentInterface $comment
     *
     * @return boolean
     */
    public function canView(CommentInterface $comment)
    {
        return true;
    }

    /**
     * Checks if the Security token is allowed to reply to a parent comment.
     *
     * @param CommentInterface|null $parent
     *
     * @return boolean
     */
    public function canReply(CommentInterface $parent = null)
    {
        /** @var $parent \Incenteev\WebBundle\Entity\Comment */
        if (null !== $parent) {
            return $this->canCreate() && $this->canView($parent) && 0 === count($parent->getAncestors());
        }

        return $this->canCreate();
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Comment.
     *
     * @param CommentInterface $comment
     *
     * @return boolean
     */
    public function canEdit(CommentInterface $comment)
    {
        return false;

        $object = $this->commentUtils->findContestForComment($comment);

        if (null === $object) {
            return true;
        }

        $token = $this->securityContext->getToken();
        if (null === $token) {
            return false;
        }
        $user = $token->getUser();

        return $comment instanceof SignedCommentInterface && $comment->getAuthor() === $user;
    }

    /**
     * Checks if the Security token is allowed to delete a specific Comment.
     *
     * @param CommentInterface $comment
     *
     * @return boolean
     */
    public function canDelete(CommentInterface $comment)
    {
        $object = $this->commentUtils->findContestForComment($comment);

        if (null === $object) {
            return true;
        }

        $token = $this->securityContext->getToken();

        if (null === $token) {
            return false;
        }

        $user = $token->getUser();

        if ($comment instanceof SignedCommentInterface && $comment->getAuthor() === $user) {
            return true;
        }

        return $object->hasOwner($user);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param CommentInterface $comment
     */
    public function setDefaultAcl(CommentInterface $comment)
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
