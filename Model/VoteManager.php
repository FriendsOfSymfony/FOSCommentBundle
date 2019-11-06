<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

use FOS\CommentBundle\Event\Event;
use FOS\CommentBundle\Event\VoteEvent;
use FOS\CommentBundle\Event\VotePersistEvent;
use FOS\CommentBundle\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;

/**
 * Abstract VotingManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
abstract class VoteManager implements VoteManagerInterface
{
    /**
     * @var
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = class_exists(LegacyEventDispatcherProxy::class) ? LegacyEventDispatcherProxy::decorate($dispatcher) : $dispatcher;
    }

    /**
     * Finds a vote by id.
     *
     * @param  $id
     *
     * @return VoteInterface
     */
    public function findVoteById($id)
    {
        return $this->findVoteBy(array('id' => $id));
    }

    /**
     * Creates a Vote object.
     *
     * @param VotableCommentInterface $comment
     *
     * @return VoteInterface
     */
    public function createVote(VotableCommentInterface $comment)
    {
        $class = $this->getClass();
        $vote = new $class();
        $vote->setComment($comment);

        $event = new VoteEvent($vote);
        $this->dispatch($event, Events::VOTE_CREATE);

        return $vote;
    }

    /**
     * @param VoteInterface $vote
     */
    public function saveVote(VoteInterface $vote)
    {
        if (null === $vote->getComment()) {
            throw new \InvalidArgumentException('Vote passed into saveVote must have a comment');
        }

        $event = new VotePersistEvent($vote);
        $this->dispatch($event, Events::VOTE_PRE_PERSIST);

        if ($event->isPersistenceAborted()) {
            return;
        }

        $this->doSaveVote($vote);

        $event = new VoteEvent($vote);
        $this->dispatch($event, Events::VOTE_POST_PERSIST);
    }

    /**
     * @param Event  $event
     * @param string $eventName
     */
    protected function dispatch(Event $event, $eventName)
    {
        // LegacyEventDispatcherProxy exists in Symfony >= 4.3
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            // New Symfony 4.3 EventDispatcher signature
            $this->dispatcher->dispatch($event, $eventName);
        } else {
            // Old EventDispatcher signature
            $this->dispatcher->dispatch($eventName, $event);
        }
    }

    /**
     * Performs the persistence of the Vote.
     *
     * @abstract
     *
     * @param VoteInterface $vote
     */
    abstract protected function doSaveVote(VoteInterface $vote);
}
