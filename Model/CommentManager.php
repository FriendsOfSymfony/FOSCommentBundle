<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

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
     * Returns an empty comment instance
     *
     * @return Comment
     */
    public function createComment()
    {
        $class = $this->getClass();
        $comment = new $class;

        return $comment;
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
    public function findCommentTreeByThread(ThreadInterface $thread, $sortOrder = 'DESC', $depth = null)
    {
        $comments = $this->findCommentsByThread($thread, 'ASC', $depth);

        return $this->organiseComments($comments, $sortOrder);
    }

    /**
     * Creates the ancestor array for a given parent
     * Gets the parent ancestors, and adds the parent id.
     *
     * @param CommentInterface $parent
     * @return array
     * @throw InvalidArgumentException if the parent has no ID
     */
    protected function createAncestors(CommentInterface $parent)
    {
        if (!$parent->getId()) {
            throw new InvalidArgumentException('The comment parent must have an ID.');
        }
        $ancestors = $parent->getAncestors();
        $ancestors[] = $parent->getId();

        return $ancestors;
    }

    /**
     * Organises a flat array of comments into a Tree structure. For
     * organising comment branches of a Tree, certain parents which
     * have not been fetched should be passed in as an array to
     * $ignoreParents.
     *
     * @param array $comments An array of comments to organise
     * @param array $ignoreParents An array of parents to ignore
     * @return array A tree of comments
     */
    protected function organiseComments($comments, $sortOrder = 'DESC', $ignoreParents = null)
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
        $tree = $tree->toArray($sortOrder);

        return $tree;
    }
}
