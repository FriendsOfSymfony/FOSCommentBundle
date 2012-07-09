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
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Assigns a FOS\UserBundle user from the logged in user to a vote.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class VoteBlamerListener implements EventSubscriberInterface
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param LoggerInterface          $logger
     */
    public function __construct(SecurityContextInterface $securityContext = null, LoggerInterface $logger = null)
    {
        $this->securityContext = $securityContext;
        $this->logger = $logger;
    }

    /**
     * Assigns the Security token's user to the vote.
     *
     * @param  VoteEvent $vote
     * @return void
     */
    public function blame(VoteEvent $event)
    {
        $vote = $event->getVote();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Vote Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$vote instanceof SignedVoteInterface) {
            if ($this->logger) {
                $this->logger->debug("Vote does not implement SignedVoteInterface, skipping");
            }

            return;
        }

        if (null === $this->securityContext->getToken()) {
            if ($this->logger) {
                $this->logger->debug("There is no firewall configured. We cant get a user.");
            }

            return;
        }

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $vote->setVoter($this->securityContext->getToken()->getUser());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::VOTE_PRE_PERSIST => 'blame');
    }
}
