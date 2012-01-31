<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentEvent;
use FOS\CommentBundle\Event\CommentPersistEvent;
use FOS\CommentBundle\Sorting\SortingFactory;
use FOS\CommentBundle\Sorting\SortingInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Abstract Comment Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class CommentManager implements CommentManagerInterface
{
    /**
     * @var SortingFactory
     */
    protected $sortingFactory;

    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \FOS\CommentBundle\Sorting\SortingFactory $factory
     */
    public function __construct(EventDispatcherInterface $dispatcher, SortingFactory $factory)
    {
        $this->dispatcher = $dispatcher;
        $this->sortingFactory = $factory;
    }

    /**
     * Returns an empty comment instance
     *
     * @return Comment
     */
    public function createComment(ThreadInterface $thread, CommentInterface $parent = null)
    {
        $class = $this->getClass();
        $comment = new $class;

        $comment->setThread($thread);

        if (null !== $parent) {
            $comment->setParent($parent);
        }

        $event = new CommentEvent($comment);
        $this->dispatcher->dispatch(Events::COMMENT_CREATE, $event);

        return $comment;
    }

    /*
     * Returns all thread comments in a nested array
     * Will typically be used when it comes to display the comments.
     *
     * @param ThreadInterface $thread
     * @param string $sorter
     * @param integer $depth
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
    public function findCommentTreeByThread(ThreadInterface $thread, $sorter = null, $depth = null)
    {
        $comments = $this->findCommentsByThread($thread, $depth);
        $sorter = $this->sortingFactory->getSorter($sorter);

        return $this->organiseComments($comments, $sorter);
    }

    /**
     * Organises a flat array of comments into a Tree structure. For
     * organising comment branches of a Tree, certain parents which
     * have not been fetched should be passed in as an array to
     * $ignoreParents.
     *
     * @param array $comments An array of comments to organise
     * @param string|null $sorter The sorter to use for sorting the tree
     * @param array|null $ignoreParents An array of parents to ignore
     * @return array A tree of comments
     */
    protected function organiseComments($comments, SortingInterface $sorter, $ignoreParents = null)
    {
        $tree = new Tree();

        foreach($comments as $comment) {
            $path = $tree;

            $ancestors = $comment->getAncestors();
            if (is_array($ignoreParents)) {
                $ancestors = array_diff($ancestors, $ignoreParents);
            }

            foreach ($ancestors as $ancestor) {
                $path = $path->traverse($ancestor);
            }

            $path->add($comment);
        }

        $tree = $tree->toArray();
        $tree = $sorter->sort($tree);

        return $tree;
    }

    /**
     * Saves a comment to the persistence backend used. Each backend
     * must implement the abstract doSaveComment method which will
     * perform the saving of the comment to the backend.
     *
     * @param CommentInterface $comment
     * @throws InvalidArgumentException when the comment does not have a thread.
     */
    public function saveComment(CommentInterface $comment)
    {
        if (null === $comment->getThread()) {
            throw new InvalidArgumentException('The comment must have a thread');
        }

        $event = new CommentPersistEvent($comment);
        $this->dispatcher->dispatch(Events::COMMENT_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return;
        }

        $this->doSaveComment($comment);

        $event = new CommentEvent($comment);
        $this->dispatcher->dispatch(Events::COMMENT_POST_PERSIST, $event);
    }

    /**
     * Performs the persistence of a comment.
     *
     * @abstract
     * @param CommentInterface $comment
     */
    abstract protected function doSaveComment(CommentInterface $comment);
}
