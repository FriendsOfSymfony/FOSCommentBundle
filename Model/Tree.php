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

/**
 * Used to create an array of comments in a tree structure.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class Tree
{
    /**
     * @var CommentInterface|null
     */
    private $comment;

    /**
     * @var array of Tree
     */
    private $children = array();

    /**
     * Constructor.
     *
     * @param CommentInterface|null $comment
     */
    public function __construct(CommentInterface $comment = null)
    {
        $this->comment = $comment;
    }

    /**
     * Adds a comment as a child of this node.
     *
     * @param  CommentInterface $comment
     * @return void
     */
    public function add(CommentInterface $comment)
    {
        $this->children[$comment->getId()] = new Tree($comment);
    }

    /**
     * Returns the Tree related to the supplied id.
     *
     * @param  mixed $id
     * @return Tree
     */
    public function traverse($id)
    {
        return $this->children[$id];
    }

    /**
     * Converts the Tree structure to arrays.
     *
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
    public function toArray()
    {
        $children = array();
        foreach ($this->children as $child) {
            $children[] = $child->toArray();
        }

        return $this->comment ? array('comment' => $this->comment, 'children' => $children) : $children;
    }
}
