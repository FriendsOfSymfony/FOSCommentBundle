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
use FOS\CommentBundle\Event\ThreadEvent;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible for setting a permalink for each new Thread object.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com
 */
class ThreadPermalinkListener implements EventSubscriberInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request       = $request;
    }

    /**
     * Creates and persists a thread with the specified id.
     *
     * @param \FOS\CommentBundle\Event\ThreadEvent $event
     */
    public function onThreadCreate(ThreadEvent $event)
    {
        $thread = $event->getThread();
        $thread->setPermalink($this->request->getUri());
    }

    static public function getSubscribedEvents()
    {
        return array(Events::THREAD_CREATE => 'onThreadCreate');
    }
}
