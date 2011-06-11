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

use FOS\CommentBundle\Sorting\SortingFactory;
use FOS\CommentBundle\Sorting\SortingInterface;
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
    private $sortingFactory;

    /**
     * Sets the SortingFactory instance on the Manager.
     *
     * @param SortingFactory $factory
     * @return void
     */
    protected function setSortingFactory(SortingFactory $factory)
    {
        $this->sortingFactory = $factory;
    }

    /**
     * Retrieves the SortingFactory.
     *
     * @return SortingFactory
     * @throws RuntimeException when no sorting factory has been set
     */
    protected function getSortingFactory()
    {
        if (null === $this->sortingFactory) {
            throw new RuntimeException('No sorting factory has been set');
        }

        return $this->sortingFactory;
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
        $sorter = $this->getSortingFactory()->getSorter($sorter);

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
}
