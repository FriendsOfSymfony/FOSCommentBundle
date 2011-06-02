<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Blamer;

use FOS\CommentBundle\Model\VoteInterface;

/**
 * Does nothing.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class NoopVoteBlamer implements VoteBlamerInterface
{
    /**
     * Nothing to see here.
     *
     * @param VoteInterface $vote
     * @return void
     */
    public function blame(VoteInterface $vote)
    {
        // Do nothing.
    }
}
