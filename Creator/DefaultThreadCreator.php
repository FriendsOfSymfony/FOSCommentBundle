<?php

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\CommentBundle\Model\ThreadManagerInterface;

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
