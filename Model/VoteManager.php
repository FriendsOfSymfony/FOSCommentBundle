<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

/**
 * Abstract VotingManager
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
abstract class VoteManager implements VoteManagerInterface
{
    /**
     * {@inheritDoc}
     */
    public function findVoteById($id)
    {
        return $this->findVoteBy(array('id' => $id));
    }

    /**
     * {@inheritDoc}
     */
    public function createVote()
    {
        $class = $this->getClass();
        $vote = new $class();

        return $vote;
    }
}