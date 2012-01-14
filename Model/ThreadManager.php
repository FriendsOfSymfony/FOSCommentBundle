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

use FOS\CommentBundle\Event\ThreadEvent;
use FOS\CommentBundle\Event\ThreadEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Abstract Thread Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class ThreadManager implements ThreadManagerInterface
{
    protected $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $id
     * @return ThreadInterface
     */
    public function findThreadById($id)
    {
        return $this->findThreadBy(array('id' => $id));
    }

    /**
     * Creates an empty comment thread instance
     *
     * @return Thread
     */
    public function createThread()
    {
        $class = $this->getClass();
        $thread = new $class;

        $event = new ThreadEvent($thread);
        $this->dispatcher->dispatch(ThreadEvents::CREATE, $event);

        return $thread;
    }

    /**
     * Handles notifiying any event listeners of an upcoming persist
     * action. Implementors must take care to handle persisting after
     * calling this method, and dispatching a POST_PERSIST event.
     *
     * @param ThreadInterface $thread
     */
    public function addThread(ThreadInterface $thread)
    {
        $event = new ThreadEvent($thread);
        $this->dispatcher->dispatch(ThreadEvents::PRE_PERSIST, $event);
    }
}
