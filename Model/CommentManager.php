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
     * @var EventDispatcherInterface
     */
    protected $dispatcher;

    /**
     * Constructor.
     *
     * @param EventDispatcherInterface $dispatcher A dispatcher instance.
     * @param SortingFactory           $factory    A factory instance.
     */
    public function __construct(EventDispatcherInterface $dispatcher, SortingFactory $factory)
    {
        $this->dispatcher = $dispatcher;
        $this->sortingFactory = $factory;
    }

    /**
     * {@inheritdoc}
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

    /**
     * {@inheritdoc}
     */
    public function findCommentTreeByThread(ThreadInterface $thread, $sorter = null, $depth = null)
    {
        $comments = $this->findCommentsByThread($thread, $depth);
        $sorter = $this->sortingFactory->getSorter($sorter);

        return $this->organiseComments($comments, $sorter);
    }

    /**
     * Organises a flat array of comments into a Tree structure.
     *
     * For organising comment branches of a Tree, certain parents which
     * have not been fetched should be passed in as an array to $ignoreParents.
     *
     * @param CommentInterface[]      $comments      An array of comments to organise
     * @param SortingInterface        $sorter        The sorter to use for sorting the tree
     * @param CommentInterface[]|null $ignoreParents An array of parents to ignore
     *
     * @return array A tree of comments
     */
    protected function organiseComments($comments, SortingInterface $sorter, $ignoreParents = null)
    {
        $tree = new Tree();

        foreach ($comments as $comment) {
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
     * {@inheritdoc}
     */
    public function saveComment(CommentInterface $comment)
    {
        if (null === $comment->getThread()) {
            throw new InvalidArgumentException('The comment must have a thread');
        }

        $event = new CommentPersistEvent($comment);
        $this->dispatcher->dispatch(Events::COMMENT_PRE_PERSIST, $event);

        if ($event->isPersistenceAborted()) {
            return false;
        }

        $this->doSaveComment($comment);

        $event = new CommentEvent($comment);
        $this->dispatcher->dispatch(Events::COMMENT_POST_PERSIST, $event);

        return true;
    }

    /**
     * Performs the persistence of a comment.
     *
     * @param CommentInterface $comment
     */
    abstract protected function doSaveComment(CommentInterface $comment);
}
