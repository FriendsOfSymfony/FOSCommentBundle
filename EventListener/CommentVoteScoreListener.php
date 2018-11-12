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

use FOS\CommentBundle\Event\VoteEvent;
use FOS\CommentBundle\Events;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * A listener that increments the comments vote score when a
 * new vote has been added.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentVoteScoreListener implements EventSubscriberInterface
{
    private $voteManager;
    private $voteUnique;

    /**
     * @param bool $voteUnique
     */
    public function __construct(VoteManagerInterface $voteManager, $voteUnique = false)
    {
        $this->voteManager = $voteManager;
        $this->voteUnique = $voteUnique;
    }

    public function onVotePersist(VoteEvent $event)
    {
        $vote = $event->getVote();
        $comment = $vote->getComment();
        if (!$comment instanceof VotableCommentInterface) {
            return;
        }

        if (true === $this->voteManager->isNewVote($vote)) {
            $comment->incrementScore($vote->getValue());
        } else if (false === $this->voteManager->isNewVote($vote) && true === $this->voteUnique) {
            $comment->incrementScore($vote->getValue() * 2);
        }
    }

    public function onVoteRemove(VoteEvent $event)
    {
        $vote = $event->getVote();
        $comment = $vote->getComment();
        if (!$comment instanceof VotableCommentInterface) {
            return;
        }

        $comment->decrementScore($vote->getValue());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::VOTE_PRE_PERSIST => 'onVotePersist',
            Events::VOTE_PRE_REMOVE => 'onVoteRemove',
        );
    }
}
