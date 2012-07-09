<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Entity;

use Doctrine\ORM\EntityManager;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\ThreadManager as BaseThreadManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM ThreadManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class ThreadManager extends BaseThreadManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \Doctrine\ORM\EntityManager                                 $em
     * @param string                                                      $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        parent::__construct($dispatcher);

        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
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
        return !$this->em->getUnitOfWork()->isInIdentityMap($thread);
    }

    /**
     * Saves a thread
     *
     * @param ThreadInterface $thread
     */
    protected function doSaveThread(ThreadInterface $thread)
    {
        $this->em->persist($thread);
        $this->em->flush();
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
