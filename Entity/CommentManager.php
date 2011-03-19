<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Entity;

use Doctrine\ORM\EntityManager;
use FOS\CommentBundle\Model\CommentManager as BaseCommentManager;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\Tree;
use InvalidArgumentException;
use DateTime;

class CommentManager extends BaseCommentManager
{
    protected $em;
    protected $repository;
    protected $class;

    /**
     * Constructor.
     *
     * @param EntityManager           $em
     * @param string                  $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository($class);
        $this->class      = $em->getClassMetadata($class)->name;
    }

    /*
     * Returns all thread comments in a nested array
     * Will typically be used when it comes to display the comments.
     *
     * Will query for an additional level of depth when provided
     * so templates can determine to display a 'load more comments' link.
     *
     * @param  string  $identifier
     * @param  integer $depth An optional depth limit
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
    public function findCommentsByThread(ThreadInterface $thread, $depth = null)
    {
        $qb = $this->repository
            ->createQueryBuilder('c')
            ->join('c.thread', 't')
            ->where('t.identifier = :thread')
            ->orderBy('c.ancestors', 'ASC')
            ->setParameter('thread', $thread->getIdentifier());

        if ($depth > 0) {
            // Queries for an additional level so templates can determine
            // if the final 'depth' layer has children.
            
            $qb->andWhere('c.depth <= :depth')
               ->setParameter('depth', $depth + 1);
        }

        $comments = $qb
            ->getQuery()
            ->execute();

        return $this->organiseComments($comments);
    }
    
    /**
     * Returns the requested comment tree branch
     *
     * @param integer $commentId 
     * @return array See findCommentsByThread
     */
    public function findCommentsByCommentId($commentId)
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

        $trimParents = current($comments)->getAncestors();
        return $this->organiseComments($comments, $trimParents);
    }
    
    protected function organiseComments($comments, $trimParents = null)
    {
        $tree = new Tree();
        foreach($comments as $index => $comment) {
            $path = $tree;
            
            $ancestors = $comment->getAncestors();
            if (is_array($trimParents))
            {
                $ancestors = array_diff($ancestors, $trimParents);
            }
            
            foreach ($ancestors as $ancestor) {
                $path = $path->traverse($ancestor);
            }
            $path->add($comment);
        }
        $tree = $tree->toArray();

        return $tree;
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
        $this->em->persist($comment);
        $this->em->flush();
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
