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
use FOS\CommentBundle\Event\CommentEvent;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a comment using Symfony2 security component
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class CommentBlamerListener implements EventSubscriberInterface
{
    /**
     * @var SecurityContext
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
     * Assigns the currently logged in user to a Comment.
     *
     * @param  \FOS\CommentBundle\Event\CommentEvent $event
     * @return void
     */
    public function blame(CommentEvent $event)
    {
        $comment = $event->getComment();

        if (null === $this->securityContext) {
            if ($this->logger) {
                $this->logger->debug("Comment Blamer did not receive the security.context service.");
            }

            return;
        }

        if (!$comment instanceof SignedCommentInterface) {
            if ($this->logger) {
                $this->logger->debug("Comment does not implement SignedCommentInterface, skipping");
            }

            return;
        }

        if (null === $this->securityContext->getToken()) {
            if ($this->logger) {
                $this->logger->debug("There is no firewall configured. We cant get a user.");
            }

            return;
        }

        if (null === $comment->getAuthor() && $this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $comment->setAuthor($this->securityContext->getToken()->getUser());
        }
    }

    public static function getSubscribedEvents()
    {
        return array(Events::COMMENT_PRE_PERSIST => 'blame');
    }
}
