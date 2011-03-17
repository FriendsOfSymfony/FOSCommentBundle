<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
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
     * @param DocumentManager         $dm
     * @param string                  $class
     */
    public function __construct(DocumentManager $dm, $class)
    {
        $this->dm         = $dm;
        $this->repository = $dm->getRepository($class);
        $this->class      = $dm->getClassMetadata($class)->name;
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
