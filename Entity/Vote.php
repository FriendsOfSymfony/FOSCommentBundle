<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Entity;

use FOS\CommentBundle\Model\Vote as BaseVote;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\UserBundle\Model\UserInterface;

/**
 * Vote
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
abstract class Vote extends BaseVote
{
    /**
     * @var VotableCommentInterface
     */
    protected $comment;

    /**
     * Gets the comment this vote belongs to.
     *
     * @return VotableCommentInterface
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the comment this vote belongs to.
     *
     * @param VotableCommentInterface $comment
     * @return void
     */

    public function setComment(VotableCommentInterface $comment)
    {
        $this->comment = $comment;
    }
}