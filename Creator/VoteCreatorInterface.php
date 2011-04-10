<?php

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteInterface;

interface VoteCreatorInterface
{
    /**
     * Creates and saves a vote against a specific comment
     *
     * @param VoteInterface $vote
     * @return bool if the vote was created successfully
     */
    function create(VoteInterface $vote, VotableCommentInterface $comment);
}
