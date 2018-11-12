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

use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VoteAccessor implements VoteAccessorInterface
{
    private $tokenStorage;
    private $voteManager;
    private $votedMap = array();

    public function __construct(VoteManagerInterface $voteManager, TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        $this->voteManager = $voteManager;
    }

    /**
     * {@inheritdoc}
     */
    public function hasVote(VotableCommentInterface $comment, UserInterface $voter = null)
    {
        return null !== $this->voteByVoter($comment, $voter);
    }

    /**
     * {@inheritdoc}
     */
    public function voteByVoter(VotableCommentInterface $comment, UserInterface $voter = null)
    {
        if (null === $voter) {
            if (null === $voter = $this->getAuthenticatedVoter()) {
                throw new \LogicException('There is no firewall configured. We cant get a user.');
            }
        }

        if (isset($this->votedMap[$comment->getId()])) {
            return $this->votedMap[$comment->getId()];
        }

        $votes = $this->voteManager->findVotesByCommentThreadAndVoter($comment, $voter);
        foreach ($votes as $vote) {
            $this->votedMap[$vote->getComment()->getId()] = $vote;
        }

        return isset($this->votedMap[$comment->getId()]) ? $this->votedMap[$comment->getId()] : null;
    }

    /**
     * @return UserInterface|null
     */
    protected function getAuthenticatedVoter()
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return null;
        }

        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return null;
        }

        return $user;
    }
}