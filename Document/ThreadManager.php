<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\ThreadManager as BaseThreadManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ODM ThreadManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ThreadManager extends BaseThreadManager
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var DocumentRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param DocumentManager $dm
     * @param string          $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, DocumentManager $dm, $class)
    {
        parent::__construct($dispatcher);

        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);

        $metadata = $dm->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Finds one comment thread by the given criteria
     *
     * @param  array           $criteria
     * @return ThreadInterface
     */
    public function findThreadBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findThreadsBy(array $criteria)
    {
        return $this->repository->findBy($criteria);
    }

    /**
     * Finds all threads.
     *
     * @return array of ThreadInterface
     */
    public function findAllThreads()
    {
        return $this->repository->findAll();
    }

    /**
     * {@inheritDoc}
     */
    public function isNewThread(ThreadInterface $thread)
    {
        return !$this->dm->getUnitOfWork()->isInIdentityMap($thread);
    }

    /**
     * Saves a thread
     *
     * @param ThreadInterface $thread
     */
    protected function doSaveThread(ThreadInterface $thread)
    {
        $this->dm->persist($thread);
        $this->dm->flush();
    }

    /**
     * Returns the fully qualified comment thread class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
