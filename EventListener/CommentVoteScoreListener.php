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

use FOS\CommentBundle\Event\VoteEvent;
use FOS\CommentBundle\Event\VoteEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommentVoteScoreListener implements EventSubscriberInterface
{
    public function onVotePersist(VoteEvent $event)
    {
        $vote = $event->getVote();
        $comment = $event->getComment();
        $comment->incrementScore($vote->getValue());
    }

    static public function getSubscribedEvents()
    {
        return array(VoteEvents::PRE_PERSIST => 'onVotePersist');
    }
}