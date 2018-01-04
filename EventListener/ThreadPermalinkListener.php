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

use FOS\CommentBundle\Event\ThreadEvent;
use FOS\CommentBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Responsible for setting a permalink for each new Thread object.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com
 */
class ThreadPermalinkListener implements EventSubscriberInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Creates and persists a thread with the specified id.
     *
     * @param \FOS\CommentBundle\Event\ThreadEvent $event
     */
    public function onThreadCreate(ThreadEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $thread = $event->getThread();
        $thread->setPermalink($request->getUri());
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(Events::THREAD_CREATE => 'onThreadCreate');
    }
}
