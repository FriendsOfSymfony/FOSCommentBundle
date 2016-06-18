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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Responsible for setting a permalink for each new Thread object.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com
 */
class ThreadPermalinkListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Creates and persists a thread with the specified id.
     *
     * @param \FOS\CommentBundle\Event\ThreadEvent $event
     */
    public function onThreadCreate(ThreadEvent $event)
    {
        $request = null;
        if ($this->container->has('request_stack')) {
            $request = $this->container->get('request_stack')->getCurrentRequest();
        } elseif (method_exists($this->container, 'isScopeActive') && $this->container->isScopeActive('request')) {
            $request = $this->container->get('request');
        }

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
