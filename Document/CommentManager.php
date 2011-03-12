<?php

namespace FOS\CommentBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\CommentManager as BaseCommentManager;

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
        parent::__construct();

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
    function findCommentsByThread(CommentThreadInterface $thread)
    {
        throw new Exception('Not implemented.');
    }
}
