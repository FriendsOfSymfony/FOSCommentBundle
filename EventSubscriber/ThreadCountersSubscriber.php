<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\EventSubscriber;

use FOS\CommentBundle\Event\CommentEvent;
use FOS\CommentBundle\Event\CommentEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ThreadCountersSubscriber implements EventSubscriberInterface
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
        return array(CommentEvents::PRE_PERSIST => 'onCommentPersist');
    }
}