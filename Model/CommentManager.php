<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

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

    /**
     * Organises a flat array of comments into a Tree structure. For
     * organising comment branches of a Tree, certain parents which
     * have not been fetched should be passed in as an array to
     * $ignoreParents.
     *
     * @param string $comments An array of comments to organise
     * @param string $ignoreParents An array of parents to ignore
     * @return array A tree of comments
     */
    protected function organiseComments($comments, $ignoreParents = null)
    {
        $tree = new Tree();

        foreach($comments as $index => $comment) {
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

        return $tree;
    }
}
