<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteInterface;

/**
 * Responsible for creation and persistence of Votes.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface VoteCreatorInterface
{
    /**
     * Validates and saves a Vote against a specific comment
     *
     * @param VoteInterface $vote
     * @return bool if the Vote was created successfully
     */
    function create(VoteInterface $vote, VotableCommentInterface $comment);
}
