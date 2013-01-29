<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Wraps a real implementation of ThreadManagerInterface and
 * performs Acl checks with the configured Thread Acl service.
 *
 * @author Tim Nagel <tim@nagel.com.au
 */
class AclThreadManager implements ThreadManagerInterface
{
    /**
     * The ThreadManager instance to be wrapped with ACL.
     *
     * @var ThreadManagerInterface
     */
    protected $realManager;

    /**
     * The Thread Acl instance for querying Acls.
     *
     * @var ThreadAclInterface
     */
    protected $threadAcl;

    /**
     * Constructor.
     *
     * @param ThreadManagerInterface $threadManager The concrete ThreadManager service
     * @param ThreadAclInterface     $threadAcl     The Thread Acl service
     */
    public function __construct(ThreadManagerInterface $threadManager, ThreadAclInterface $threadAcl)
    {
        $this->realManager = $threadManager;
        $this->threadAcl   = $threadAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function findThreadById($id)
    {
        $thread = $this->realManager->findThreadById($id);

        if (null !== $thread && !$this->threadAcl->canView($thread)) {
            throw new AccessDeniedException();
        }

        return $thread;
    }

    /**
     * {@inheritDoc}
     */
    public function findThreadBy(array $criteria)
    {
        $thread = $this->realManager->findThreadBy($criteria);

        if (null !== $thread && !$this->threadAcl->canView($thread)) {
            throw new AccessDeniedException();
        }

        return $thread;
    }

    /**
     * {@inheritDoc}
     */
    public function findThreadsBy(array $criteria)
    {
        $threads = $this->realManager->findThreadsBy($criteria);

        foreach ($threads as $thread) {
            if (!$this->threadAcl->canView($thread)) {
                throw new AccessDeniedException();
            }
        }

        return $threads;
    }

    /**
     * {@inheritDoc}
     */
    public function findAllThreads()
    {
        $threads = $this->realManager->findAllThreads();

        foreach ($threads as $thread) {
            if (!$this->threadAcl->canView($thread)) {
                throw new AccessDeniedException();
            }
        }

        return $threads;
    }

    /**
     * {@inheritDoc}
     */
    public function createThread($id = null)
    {
        return $this->realManager->createThread($id);
    }

    /**
     * {@inheritDoc}
     */
    public function saveThread(ThreadInterface $thread)
    {
        if (!$this->threadAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        $newThread = $this->isNewThread($thread);

        if (!$newThread && !$this->threadAcl->canEdit($thread)) {
            throw new AccessDeniedException();
        }

        $this->realManager->saveThread($thread);

        if ($newThread) {
            $this->threadAcl->setDefaultAcl($thread);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function isNewThread(ThreadInterface $thread)
    {
        return $this->realManager->isNewThread($thread);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }
}
