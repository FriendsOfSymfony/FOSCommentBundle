<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\CommentBundle\Model\CommentManager as BaseCommentManager;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\Tree;
use InvalidArgumentException;
use DateTime;
use Exception;

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
     * Returns all thread comments in a nested array
     * Will typically be used when it comes to display the comments.
     *
     * @param  string $identifier
     * @return array(
     *     0 => array(
     *         'comment' => CommentInterface,
     *         'children' => array(
     *             0 => array (
     *                 'comment' => CommentInterface,
     *                 'children' => array(...)
     *             ),
     *             1 => array (
     *                 'comment' => CommentInterface,
     *                 'children' => array(...)
     *             )
     *         )
     *     ),
     *     1 => array(
     *         ...
     *     )
     */
    public function findCommentTreeByThread(ThreadInterface $thread, $depth = null)
    {
        $comments = $this->findCommentsByThread($thread, $depth);
        return $this->organiseComments($comments);
    }

    /**
     * Returns a flat array of comments of a specific thread.
     *
     * @param ThreadInterface $thread
     * @param integer $depth
     * @return array of ThreadInterface
     */
    public function findCommentsByThread(ThreadInterface $thread, $depth = null)
    {
        $qb = $this->repository
            ->createQueryBuilder()
            ->field('thread.$id')->equals($thread->getIdentifier())
            ->sort('ancestors', 'ASC');

        if ($depth > 0) {
            // Queries for an additional level so templates can determine
            // if the final 'depth' layer has children.

            $qb->field('depth')->lte($depth + 1);
        }

        $comments = $qb
            ->getQuery()
            ->execute();

        return $comments;
    }

    /**
     * Returns the requested comment tree branch
     *
     * @param integer $commentId
     * @return array See findCommentsByThread
     */
    public function findCommentTreeByCommentId($commentId)
    {
        $qb = $this->repository
            ->createQueryBuilder()
            ->field('ancestors')->equals($commentId)
            ->sort('ancestors', 'ASC');

        $comments = $qb->getQuery()->execute();

        if (!$comments) {
            return array();
        }

        $ignoreParents = $comments->getSingleResult()->getAncestors();
        return $this->organiseComments($comments, $ignoreParents);
    }

    /**
     * Adds a comment
     *
     * @param CommentInterface $comment
     * @param CommentInterface $parent Only used when replying to a specific CommentInterface
     */
    public function addComment(CommentInterface $comment, CommentInterface $parent = null)
    {
        if (null !== $comment->getId()) {
            throw new InvalidArgumentException('Can not add already saved comment');
        }
        if (null === $comment->getThread()) {
            throw new InvalidArgumentException('The comment must have a thread');
        }
        if (null !== $parent) {
            $comment->setAncestors($this->createAncestors($parent));
        }
        $thread = $comment->getThread();
        $thread->setNumComments($thread->getNumComments() + 1);
        $thread->setLastCommentAt(new DateTime());
        $this->dm->persist($comment);
        $this->dm->flush();
    }

    /**
     * Creates the ancestor array for a given parent
     * Gets the parent ancestors, and adds the parent id.
     *
     * @param CommentInterface $parent
     * @return array
     * @throw InvalidArgumentException if the parent has no ID
     */
    private function createAncestors(CommentInterface $parent)
    {
        if (!$parent->getId()) {
            throw new InvalidArgumentException('The comment parent must have an ID.');
        }
        $ancestors = $parent->getAncestors();
        $ancestors[] = $parent->getId();

        return $ancestors;
    }

    /**
     * Find one comment by its ID
     *
     * @return Comment or null
     **/
    public function findCommentById($id)
    {
        return $this->repository->find($id);
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
