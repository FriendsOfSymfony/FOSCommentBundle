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

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\VoteEvent;
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
        $vote = new $class();
        $vote->setComment($comment);

        $event = new VoteEvent($vote);
        $this->dispatcher->dispatch(Events::VOTE_CREATE, $event);

        return $vote;
    }

    public function addVote(VoteInterface $vote)
    {
        $event = new VoteEvent($vote);
        $this->dispatcher->dispatch(Events::VOTE_PRE_PERSIST, $event);

        $this->doAddVote($vote);

        $event = new VoteEvent($vote);
        $this->dispatcher->dispatch(Events::VOTE_POST_PERSIST, $event);
    }

    /**
     * Performs the persistence of the Vote.
     * 
     * @abstract
     * @param VoteInterface $vote
     */
    abstract protected function doAddVote(VoteInterface $vote);
}