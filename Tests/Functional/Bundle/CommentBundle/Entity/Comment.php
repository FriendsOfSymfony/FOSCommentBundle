<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Functional\Bundle\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\SignedCommentInterface;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_comment")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @author Tim Nagel <tim@nagel.com.au>
 */
class Comment extends BaseComment implements SignedCommentInterface, VotableCommentInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Thread of this comment
     *
     * @ORM\ManyToOne(targetEntity="Thread")
     * @var Thread
     */
    protected $thread;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $author;

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $score = 0;

    /**
     * @return Thread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param  Thread $thread
     * @return null
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }

    /**
     * Sets the author of the Comment
     *
     * @param string $user
     */
    public function setAuthor(UserInterface $author)
    {
        $this->author = $author->getUsername();
    }

    /**
     * Gets the author of the Comment
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the score of the comment.
     *
     * @param integer $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * Returns the current score of the comment.
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Increments the comment score by the provided
     * value.
     *
     * @param integer value
     *
     * @return integer The new comment score
     */
    public function incrementScore($by = 1)
    {
        $this->score += $by;
    }

    public function getAuthorName()
    {
        return $this->author ?: parent::getAuthorName();
    }
}
