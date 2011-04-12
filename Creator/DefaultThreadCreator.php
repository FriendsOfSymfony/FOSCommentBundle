<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible for the c
 */
class DefaultThreadCreator implements ThreadCreatorInterface
{
    protected $request;
    protected $threadManager;

    public function __construct(Request $request, ThreadManagerInterface $threadManager)
    {
        $this->request       = $request;
        $this->threadManager = $threadManager;
    }

    public function create($identifier)
    {
        $thread = $this->threadManager->createThread();
        $thread->setIdentifier($identifier);
        $thread->setPermalink($this->request->getUri());
        $this->threadManager->addThread($thread);

        return $thread;
    }
}
