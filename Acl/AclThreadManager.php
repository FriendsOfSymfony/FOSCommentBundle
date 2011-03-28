<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\ThreadManagerInterface;

class AclThreadManager implements ThreadManagerInterface
{
    /**
     * The ThreadManager instance to be wrapped with ACL.
     *
     * @var ThreadManagerInterface
     */
    private $realManager;

    /**
     * The Thread Acl instance for querying Acls.
     *
     * @var ThreadAclInterface
     */
    private $threadAcl;

    /**
     * {@inheritDoc}
     */
    public function __construct(ThreadManagerInterface $threadManager, ThreadAclInterface $threadAcl)
    {
        $this->realManager = $threadManager;
        $this->threadAcl   = $threadAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function findThreadByIdentifier($identifier)
    {
        $thread = $this->realManager->findThreadByIdentifier($identifier);

        if (null !== $thread) {
            $this->threadAcl->canView($thread);
        }

        return $thread;
    }

    /**
     * {@inheritDoc}
     */
    public function findThreadBy(array $criteria)
    {
        $thread = $this->realManager->findThreadBy($criteria);

        if (null !== $thread) {
            $this->threadAcl->canView($thread);
        }

        return $thread;
    }

    /**
     * {@inheritDoc}
     */
    public function findAllThreads()
    {
        $threads = $this->realManager->findAllThreads();
        foreach ($threads AS $thread) {
            $this->threadAcl->canView($thread);
        }

        return $threads;
    }

    /**
     * {@inheritDoc}
     */
    public function createThread()
    {
        return $this->realManager->createThread();
    }

    /**
     * {@inheritDoc}
     */
    public function addThread(ThreadInterface $thread)
    {
        $this->threadAcl->canCreate();

        $this->realManager->addThread($thread);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }
}
