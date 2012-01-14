<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

use FOS\CommentBundle\Event\VoteEvent;
use FOS\CommentBundle\Event\VoteEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract VotingManager
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
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function findVoteById($id)
    {
        return $this->findVoteBy(array('id' => $id));
    }

    /**
     * {@inheritDoc}
     */
    public function createVote(VotableCommentInterface $comment)
    {
        $class = $this->getClass();
        $vote = new $class($comment);

        $event = new VoteEvent($vote);
        $this->dispatcher->dispatch(VoteEvents::CREATE, $event);

        return $vote;
    }

    public function addVote(VoteInterface $vote)
    {
        $event = new VoteEvent($vote);
        $this->dispatcher->dispatch(VoteEvents::PRE_PERSIST, $event);
    }

}