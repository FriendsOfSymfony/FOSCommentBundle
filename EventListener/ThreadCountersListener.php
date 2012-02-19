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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * A listener that updates thread counters when a new comment is made.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class ThreadCountersListener implements EventSubscriberInterface
{
    public function onCommentPersist(CommentEvent $event)
    {
        $comment = $event->getComment();
        $thread = $comment->getThread();

        $thread->incrementNumComments(1);
        $thread->setLastCommentAt($comment->getCreatedAt());
    }

    static public function getSubscribedEvents()
    {
        return array(Events::COMMENT_PRE_PERSIST => 'onCommentPersist');
    }
}
