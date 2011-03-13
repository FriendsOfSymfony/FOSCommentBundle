<?php

namespace FOS\CommentBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\CommentBundle\Model\CommentManager as BaseCommentManager;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\CommentInterface;
use InvalidArgumentException;

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
    public function findCommentsByThread(ThreadInterface $thread)
    {
        $comments = $this->repository
            ->createQueryBuilder()
            ->select('_id', 'body', 'ancestors')
            ->field('thread.$id')->equals($thread->getIdentifier())
            ->sort('ancestors', 'ASC')
            ->getQuery()
            ->execute();

        $tree = new Tree();
        foreach($comments as $index => $comment) {
            $path = $tree;
            foreach ($comment->getAncestors() as $ancestor) {
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
     * Returns the fully qualified comment thread class name
     *
     * @return string
     **/
    public function getClass()
    {
        return $this->class;
    }
}
