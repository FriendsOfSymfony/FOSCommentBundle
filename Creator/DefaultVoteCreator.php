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

use FOS\CommentBundle\Blamer\VoteBlamerInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;
use Symfony\Component\Validator\ValidatorInterface;

/**
 * Manages the creation and persistence of Vote objects.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class DefaultVoteCreator implements VoteCreatorInterface
{
    protected $voteBlamer;
    protected $voteManager;

    /**
     * Constructor.
     *
     * @param VoteManagerInterface $voteManager
     * @param VoteBlamerInterface $voteBlamer
     * @param Validator $validator
     */
    public function __construct(VoteManagerInterface $voteManager, VoteBlamerInterface $voteBlamer, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->voteBlamer = $voteBlamer;
        $this->voteManager = $voteManager;
    }

    /**
     * Validates that a vote is suitable for persisting and persists
     * the Vote.
     *
     * TODO: rate-limiting addition of votes
     *
     * @param VoteInterface $vote
     * @param VotableCommentInterface $comment
     * @return bool
     */
    public function create(VoteInterface $vote, VotableCommentInterface $comment)
    {
        $this->voteBlamer->blame($vote);

        if (count($this->validator->validate($vote))) {
            return false;
        }

        $this->voteManager->addVote($vote, $comment);

        return true;
    }
}
