<?php

namespace Incenteev\WebBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Incenteev\WebBundle\Doctrine\Repository\CommentRepository")
 */
class Comment extends BaseComment implements SignedCommentInterface
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Thread
     *
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\Thread")
     */
    protected $thread;

    /**
     * @var User Author of the comment
     *
     * @ORM\ManyToOne(targetEntity="Incenteev\WebBundle\Entity\User")
     */
    protected $author;

    /**
     * Sets the author of the Comment
     *
     * @param UserInterface $author
     */
    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    /**
     * Gets the author of the Comment
     *
     * @return UserInterface
     */
    public function getAuthor()
    {
        return $this->author;
    }

    public function getAuthorName()
    {
        if (null === $this->getAuthor()) {
            return 'Anonymous';
        }

        return $this->getAuthor()->getName();
    }
}
