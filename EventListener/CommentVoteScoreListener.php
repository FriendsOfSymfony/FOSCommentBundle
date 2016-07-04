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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * A listener that increments the comments vote score when a
 * new vote has been added.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentVoteScoreListener implements EventSubscriberInterface
{
    public function onVotePersist(VoteEvent $event)
    {
        $vote = $event->getVote();
        $comment = $vote->getComment();
        $comment->incrementScore($vote->getValue());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(Events::VOTE_PRE_PERSIST => 'onVotePersist');
    }
}
