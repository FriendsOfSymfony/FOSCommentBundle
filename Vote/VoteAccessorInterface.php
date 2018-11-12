<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Vote;

use FOS\CommentBundle\Model\SignedVoteInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface VoteAccessorInterface
{
    /**
     * @return bool
     */
    public function hasVote(VotableCommentInterface $comment, UserInterface $voter = null);

    /**
     * @return SignedVoteInterface|null
     */
    public function voteByVoter(VotableCommentInterface $comment, UserInterface $voter = null);
}