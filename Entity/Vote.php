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
    protected $comment;

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment(VotableCommentInterface $comment)
    {
        $this->comment = $comment;
    }
}