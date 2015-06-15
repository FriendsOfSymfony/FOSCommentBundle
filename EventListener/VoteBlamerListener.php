<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\EventListener;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\VoteEvent;
use FOS\CommentBundle\Model\SignedVoteInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Assigns a FOS\UserBundle user from the logged in user to a vote.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class VoteBlamerListener implements EventSubscriberInterface
{
    /**
     * @var AuthorizationCheckerInterface|SecurityContextInterface
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface|SecurityContextInterface
     */
    private $tokenStorage;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param AuthorizationCheckerInterface|SecurityContextInterface $authorizationChecker
     * @param SecurityContextInterface|SecurityContextInterface      $tokenStorage
     * @param LoggerInterface                                        $logger
     */
    public function __construct($authorizationChecker, $tokenStorage, LoggerInterface $logger = null)
    {
        if (!$authorizationChecker instanceof AuthorizationCheckerInterface && !$authorizationChecker instanceof SecurityContextInterface) {
            throw new \InvalidArgumentException('Argument 1 should be an instance of Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface or Symfony\Component\Security\Core\SecurityContextInterface');
        }

        if (!$tokenStorage instanceof TokenStorageInterface && !$tokenStorage instanceof SecurityContextInterface) {
            throw new \InvalidArgumentException('Argument 2 should be an instance of Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface or Symfony\Component\Security\Core\SecurityContextInterface');
        }

        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
        $this->logger = $logger;
    }

    /**
     * Assigns the Security token's user to the vote.
     *
     * @param VoteEvent $vote
     */
    public function blame(VoteEvent $event)
    {
        $vote = $event->getVote();

        if (!$vote instanceof SignedVoteInterface) {
            if ($this->logger) {
                $this->logger->debug("Vote does not implement SignedVoteInterface, skipping");
            }

            return;
        }

        if (null === $this->tokenStorage->getToken()) {
            if ($this->logger) {
                $this->logger->debug("There is no firewall configured. We cant get a user.");
            }

            return;
        }

        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $vote->setVoter($this->tokenStorage->getToken()->getUser());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::VOTE_PRE_PERSIST => 'blame');
    }
}
