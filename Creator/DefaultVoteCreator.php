<?php

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Blamer\VoteBlamerInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;
use Symfony\Component\Validator\Validator;

class DefaultVoteCreator implements VoteCreatorInterface
{
    protected $voteBlamer;
    protected $voteManager;

    public function __construct(VoteManagerInterface $voteManager, VoteBlamerInterface $voteBlamer, Validator $validator)
    {
        $this->validator = $validator;
        $this->voteBlamer = $voteBlamer;
        $this->voteManager = $voteManager;
    }

    public function create(VoteInterface $vote, VotableCommentInterface $comment)
    {
        $this->voteBlamer->blame($vote);

        if (!$this->validator->validate($vote)) {
            return false;
        }

        $this->voteManager->addVote($vote, $comment);

        return true;
    }
}
