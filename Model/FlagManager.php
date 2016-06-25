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

use FOS\CommentBundle\Event\FlagEvent;
use FOS\CommentBundle\Event\FlagPersistEvent;
use FOS\CommentBundle\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract VotingManager
 *
 * @author Hubert Bryłkowski <hubert@brylkowski.com>
 */
abstract class FlagManager implements FlagManagerInterface
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
     * Persists a flag.
     *
     * @param  FlagInterface $flag
     * @return void
     */
    public function saveFlag(FlagInterface $flag)
    {
        if (null === $flag->getComment()) {
            throw new \InvalidArgumentException('Flag passed into saveFlag must have a comment');
        }

        $event = new FlagPersistEvent($flag);
        $this->dispatcher->dispatch(Events::FLAG_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return;
        }

        $this->doSaveFlag($flag);

        $event = new FlagEvent($flag);
        $this->dispatcher->dispatch(Events::FLAG_POST_PERSIST, $event);
    }

    /**
     * Finds a flag by id.
     *
     * @param  $id
     * @return FlagInterface
     */
    public function findFlagById($id)
    {
        $this->findFlagBy(['id' => $id]);
    }

    abstract protected function doSaveFlag(FlagInterface $flag);


}
