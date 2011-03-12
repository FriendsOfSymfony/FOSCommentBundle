<?php

namespace FOS\CommentBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\CommentThreadManager as BaseCommentThreadManager;

class CommentThreadManager extends BaseCommentThreadManager
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
        parent::__construct();

        $this->dm         = $dm;
        $this->repository = $dm->getRepository($class);
        $this->class      = $dm->getClassMetadata($class)->name;
    }

    /**
     * Finds one comment thread by the given criteria
     *
     * @param array $criteria
     * @return CommentThreadInterface
     */
    function findCommentThreadBy(array $criteria)
    {
        return $this->findOneBy($criteria);
    }

    /**
     * Adds a comment in a thread
     *
     * @param CommentThreadInterface $commentThread
     * @param CommentInterface $comment
     * @param CommentInterface $parent Only used when replying to a specific CommentInterface
     */
    public function addComment(CommentThreadInterface $commentThread, CommentInterface $comment, CommentInterface $parent = null)
    {
        throw new \Exception('Not implemented');
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
