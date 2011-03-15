<?php

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
     * If no thread is found, one is created, persisted and flushed
     * @param string $identifier
     * @return ThreadInterface
     */
    public function findThreadByIdentifierOrCreate($identifier)
    {
        $thread = $this->findThreadByIdentifier($identifier);
        if (!$thread) {
            $thread = $this->createThread();
            $thread->setIdentifier($identifier);
            $this->dm->persist($thread);
            $this->dm->flush();
        }

        return $thread;
    }

    /**
     * Saves a new thread
     *
     * @param ThreadInterface $thread
     */
    function addThread(ThreadInterface $thread)
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
