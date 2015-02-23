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
use FOS\CommentBundle\Event\ThreadEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract Thread Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class ThreadManager implements ThreadManagerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param  string          $id
     * @return ThreadInterface
     */
    public function findThreadById($id)
    {
        return $this->findThreadBy(array('id' => $id));
    }

    /**
     * Creates an empty comment thread instance
     *
     * @param bool $id
     *
     * @return Thread
     */
    public function createThread($id = null)
    {
        $class = $this->getClass();
        $thread = new $class;

        if (null !== $id) {
            $thread->setId($id);
        }

        $event = new ThreadEvent($thread);
        $this->dispatcher->dispatch(Events::THREAD_CREATE, $event);

        return $thread;
    }

    /**
     * Persists a thread.
     *
     * @param ThreadInterface $thread
     */
    public function saveThread(ThreadInterface $thread)
    {
        $event = new ThreadEvent($thread);
        $this->dispatcher->dispatch(Events::THREAD_PRE_PERSIST, $event);

        $this->doSaveThread($thread);

        $event = new ThreadEvent($thread);
        $this->dispatcher->dispatch(Events::THREAD_POST_PERSIST, $event);
    }

    /**
     * Performs the persistence of the Thread.
     *
     * @abstract
     * @param ThreadInterface $thread
     */
    abstract protected function doSaveThread(ThreadInterface $thread);
}
