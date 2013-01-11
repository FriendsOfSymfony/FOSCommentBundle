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
use FOS\CommentBundle\Model\CommentManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * A listener that updates thread counters when a new comment is made.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class ThreadCountersListener implements EventSubscriberInterface
{
    /**
     * @var CommentManagerInterface
     */
    private $commentManager;

    /**
     * Constructor.
     *
     * @param CommentManagerInterface $commentManager
     */
    public function __construct(CommentManagerInterface $commentManager)
    {
        $this->commentManager = $commentManager;
    }

    /**
     * Increase the thread comments number
     *
     * @param \FOS\CommentBundle\Event\CommentEvent $event
     */
    public function onCommentPersist(CommentEvent $event)
    {
        $comment = $event->getComment();

        if (!$this->commentManager->isNewComment($comment)) {
            return;
        }

        $thread = $comment->getThread();
        $thread->incrementNumComments(1);
        $thread->setLastCommentAt($comment->getCreatedAt());
    }

    public static function getSubscribedEvents()
    {
        return array(Events::COMMENT_PRE_PERSIST => 'onCommentPersist');
    }
}
