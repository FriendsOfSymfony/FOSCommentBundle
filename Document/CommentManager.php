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
use FOS\CommentBundle\Model\CommentManager as BaseCommentManager;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Sorting\SortingFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use DateTime;
use InvalidArgumentException;

/**
 * Default ODM CommentManager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class CommentManager extends BaseCommentManager
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
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \FOS\CommentBundle\Sorting\SortingFactory $factory
     * @param \Doctrine\ODM\MongoDB\DocumentManager $dm
     * @param string $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, SortingFactory $factory, DocumentManager $dm, $class)
    {
        parent::__construct($dispatcher, $factory);

        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);

        $metadata = $dm->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Returns a flat array of comments of a specific thread.
     *
     * @param ThreadInterface $thread
     * @param integer $depth
     * @return array of ThreadInterface
     */
    public function findCommentsByThread(ThreadInterface $thread, $depth = null, $sorterAlias = null)
    {
        $qb = $this->repository
            ->createQueryBuilder()
            ->field('thread.$id')->equals($thread->getId())
            ->sort('ancestors', 'ASC');

        if ($depth > 0) {
            // Queries for an additional level so templates can determine
            // if the final 'depth' layer has children.

            $qb->field('depth')->lte($depth + 1);
        }

        $comments = $qb
            ->getQuery()
            ->execute();

        if (null !== $sorterAlias) {
            $sorter = $this->sortingFactory->getSorter($sorterAlias);
            $comments = $sorter->sortFlat($comments);
        }

        return $comments;
    }

    /**
     * Returns the requested comment tree branch
     *
     * @param integer $commentId
     * @param string $sorter
     * @return array See findCommentsByThread
     */
    public function findCommentTreeByCommentId($commentId, $sorter = null)
    {
        $qb = $this->repository
            ->createQueryBuilder()
            ->field('ancestors')->equals($commentId)
            ->sort('ancestors', 'ASC');

        $comments = $qb->getQuery()->execute();

        if (!$comments) {
            return array();
        }

        $sorter = $this->sortingFactory->getSorter($sorter);

        $ignoreParents = $comments->getSingleResult()->getAncestors();
        return $this->organiseComments($comments, $sorter, $ignoreParents);
    }

    /**
     * Adds a comment
     *
     * @param CommentInterface $comment
     */
    protected function doSaveComment(CommentInterface $comment)
    {
        $this->dm->persist($comment->getThread());
        $this->dm->persist($comment);
        $this->dm->flush();
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
     * {@inheritDoc}
     */
    public function isNewComment(CommentInterface $comment)
    {
        return !$this->dm->getUnitOfWork()->isInIdentityMap($comment);
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
