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
 * Applies an owner to a vote.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface VoteBlamerInterface
{
    /**
     * Assigns a User to the vote.
     *
     * @param VoteInterface $vote
     * @return void
     */
    function blame(VoteInterface $vote);
}
