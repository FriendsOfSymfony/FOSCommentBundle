<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible for the creation and persistence of Thread objects.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com
 */
class DefaultThreadCreator implements ThreadCreatorInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ThreadManagerInterface
     */
    protected $threadManager;

    /**
     * Constructor.
     *
     * @param Request $request
     * @param ThreadManagerInterface $threadManager
     */
    public function __construct(Request $request, ThreadManagerInterface $threadManager)
    {
        $this->request       = $request;
        $this->threadManager = $threadManager;
    }

    /**
     * Creates and persists a thread with the specified id.
     *
     * @param mixed $id
     * @return ThreadInterface
     */
    public function create($id)
    {
        $thread = $this->threadManager->createThread();
        $thread->setId($id);
        $thread->setPermalink($this->request->getUri());
        $this->threadManager->addThread($thread);

        return $thread;
    }
}
