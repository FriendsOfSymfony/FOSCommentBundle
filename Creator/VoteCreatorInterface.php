<?php

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteInterface;

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
