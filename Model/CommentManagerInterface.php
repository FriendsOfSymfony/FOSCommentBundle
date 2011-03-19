<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

/**
 * Interface to be implemented by comment managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface CommentManagerInterface
{
    /*
     * Returns all thread comments in a nested array
     * Will typically be used when it comes to display the comments.
     *
     * Will query for an addtional level of depth when provided
     * so templates can determine to display a 'load more comments' link.
     *
     * @param  string  $identifier
     * @param  integer $depth
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
    function findCommentsByThread(ThreadInterface $thread, $depth = null);

    /**
     * Adds a comment in a thread
     *
     * @param CommentInterface $comment
     * @param CommentInterface $parent Only used when replying to a specific CommentInterface
     */
    function addComment(CommentInterface $comment, CommentInterface $parent = null);

    /**
     * Find one comment by its ID
     *
     * @return Comment or null
     **/
    function findCommentById($id);

    /**
     * Creates an empty comment instance
     *
     * @return Comment
     */
    function createComment();

    /**
     * Returns the comment fully qualified class name
     *
     * @return string
     */
    function getClass();
}
