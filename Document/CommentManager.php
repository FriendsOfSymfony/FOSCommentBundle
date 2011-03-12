<?php

namespace FOS\CommentBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\CommentBundle\Model\CommentManager as BaseCommentManager;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\CommentInterface;

class CommentManager extends BaseCommentManager
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

    /*
     * Common, strategy agnostic method to get all nested comments.
     * Will typically be used when it comes to display the comments.
     *
     * @param  string $identifier
     * @return array(
     *     'comment' => CommentInterface,
     *     'children' => array(
     *         0 => array (
     *             'comment' => CommentInterface,
     *             'children' => array(...)
     *         ),
     *         1 => array (
     *             'comment' => CommentInterface,
     *             'children' => array(...)
     *         )
     *     )
     */
    function findCommentsByThread(ThreadInterface $thread)
    {
        throw new Exception('Not implemented.');
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
