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
use FOS\CommentBundle\Model\CommentManager as BaseCommentManager;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Sorting\SortingFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Default ORM CommentManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentManager extends BaseCommentManager
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
     * @param EventDispatcherInterface $dispatcher
     * @param SortingFactory           $factory
     * @param EntityManager            $em
     * @param string                   $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, SortingFactory $factory, EntityManager $em, $class)
    {
        parent::__construct($dispatcher, $factory);

        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * {@inheritdoc}
     */
    public function findCommentsByThread(ThreadInterface $thread, $depth = null, $sorterAlias = null)
    {
        $qb = $this->repository
                ->createQueryBuilder('c')
                ->join('c.thread', 't')
                ->where('t.id = :thread')
                ->orderBy('c.ancestors', 'ASC')
                ->setParameter('thread', $thread->getId());

        if (null !== $depth && $depth >= 0) {
            // Queries for an additional level so templates can determine
            // if the final 'depth' layer has children.

            $qb->andWhere('c.depth < :depth')
               ->setParameter('depth', $depth + 1);
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
     * {@inheritdoc}
     */
    public function findCommentTreeByCommentId($commentId, $sorter = null)
    {
        $qb = $this->repository->createQueryBuilder('c');
        $qb->join('c.thread', 't')
           ->where('LOCATE(:path, CONCAT(\'/\', CONCAT(c.ancestors, \'/\'))) > 0')
           ->orderBy('c.ancestors', 'ASC')
           ->setParameter('path', "/{$commentId}/");

        $comments = $qb->getQuery()->execute();

        if (!$comments) {
            return array();
        }

        $sorter = $this->sortingFactory->getSorter($sorter);

        $trimParents = current($comments)->getAncestors();

        return $this->organiseComments($comments, $sorter, $trimParents);
    }

    /**
     * Performs persisting of the comment.
     *
     * @param CommentInterface $comment
     */
    protected function doSaveComment(CommentInterface $comment)
    {
        $this->em->persist($comment->getThread());
        $this->em->persist($comment);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function findCommentById($id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function isNewComment(CommentInterface $comment)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($comment);
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }
}
