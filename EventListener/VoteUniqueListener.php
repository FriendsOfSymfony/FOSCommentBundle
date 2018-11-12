<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\EventListener;

use FOS\CommentBundle\Event\VotePersistEvent;
use FOS\CommentBundle\Events;
use FOS\CommentBundle\Model\SignedVoteInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VoteUniqueListener implements EventSubscriberInterface
{
    private $tokenStorage;
    private $voteManager;
    private $logger;

    public function __construct(VoteManagerInterface $voteManager, TokenStorageInterface $tokenStorage, LoggerInterface $logger = null)
    {
        $this->voteManager = $voteManager;
        $this->tokenStorage = $tokenStorage;
        $this->logger = null === $logger ? new NullLogger() : $logger;
    }

    public function validatePersistence(VotePersistEvent $event)
    {
        /** @var $vote SignedVoteInterface */
        $vote = $event->getVote();
        if (false === $this->voteManager->isNewVote($vote)) {
            return;
        }

        if (!$vote instanceof SignedVoteInterface) {
            $this->stopPersistence($event);
            $this->logger->debug(sprintf('Vote does not implement "%s", skipping', SignedVoteInterface::class));

            return;
        }

        $user = null === $vote->getVoter() ? $this->tokenStorage->getToken()->getUser() : $vote->getVoter();
        if (!$user instanceof UserInterface) {
            $this->stopPersistence($event);
            $this->logger->debug('There is no firewall configured. We cant get a user.');

            return;
        }

        $comment = $vote->getComment();
        $existingVote = $this->voteManager->findVoteBy([
            'comment' => $comment,
            'voter' => $user,
        ]);

        if (null !== $existingVote) {
            $this->stopPersistence($event);
            $this->logger->debug(sprintf(
                'User "%s" already added a vote for comment with ID "%s"', $user->getUsername(), $comment->getId()
            ));
        }
    }

    private function stopPersistence(VotePersistEvent $event)
    {
        $event->abortPersistence();
        $event->stopPropagation();
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::VOTE_PRE_PERSIST => array('validatePersistence', -1)
        );
    }
}