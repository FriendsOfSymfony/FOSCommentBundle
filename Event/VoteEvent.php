<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Event;

use FOS\CommentBundle\Model\VoteInterface;
use Symfony\Component\EventDispatcher\Event;

class VoteEvent extends Event
{
    private $vote;

    /**
     * Constructs an event.
     *
     * @param \FOS\CommentBundle\Model\VoteInterface $vote
     */
    public function __construct(VoteInterface $vote)
    {
        $this->vote = $vote;
    }

    public function getVote()
    {
        return $this->vote;
    }
}