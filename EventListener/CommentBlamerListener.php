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
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Blames a comment using Symfony2 security component
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class CommentBlamerListener implements EventSubscriberInterface
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
     * Assigns the currently logged in user to a Comment.
     *
     * @param \FOS\CommentBundle\Event\CommentEvent $event
     */
    public function blame(CommentEvent $event)
    {
        $comment = $event->getComment();

        if (!$comment instanceof SignedCommentInterface) {
            if ($this->logger) {
                $this->logger->debug("Comment does not implement SignedCommentInterface, skipping");
            }

            return;
        }

        if (null === $this->tokenStorage->getToken()) {
            if ($this->logger) {
                $this->logger->debug("There is no firewall configured. We cant get a user.");
            }

            return;
        }

        if (null === $comment->getAuthor() && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $comment->setAuthor($this->tokenStorage->getToken()->getUser());
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(Events::COMMENT_PRE_PERSIST => 'blame');
    }
}
