<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Entity;

use Doctrine\ORM\EntityManager;
use FOS\CommentBundle\Model\ThreadManager as BaseThreadManager;
use FOS\CommentBundle\Model\ThreadInterface;

class ThreadManager extends BaseThreadManager
{
    protected $dm;
    protected $repository;
    protected $class;

    /**
     * Constructor.
     *
     * @param EntityManager     $em
     * @param string            $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository($class);
        $this->class      = $em->getClassMetadata($class)->name;
    }

    /**
     * Finds one comment thread by the given criteria
     *
     * @param array $criteria
     * @return ThreadInterface
     */
    public function findThreadBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Saves a new thread
     *
     * @param ThreadInterface $thread
     */
    public function addThread(ThreadInterface $thread)
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
