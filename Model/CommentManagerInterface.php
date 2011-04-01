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
    /**
     * Returns a flat array of comments with the specified thread.
     *
     * @param ThreadInterface $thread
     * @param integer $depth
     * @return array of CommentInterface
     */
    function findCommentsByThread(ThreadInterface $thread, $sortOrder = 'DESC', $depth = null);

    /*
     * Returns all thread comments in a nested array
     * Will typically be used when it comes to display the comments.
     *
     * Will query for an additional level of depth when provided
     * so templates can determine to display a 'load more comments' link.
     *
     * @param  ThreadInterface $thread
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
    function findCommentTreeByThread(ThreadInterface $thread, $sortOrder = 'DESC', $depth = null);

    /**
     * Returns a partial comment tree based on a specific parent commentId.
     *
     * @param integer $commentId
     */
    function findCommentTreeByCommentId($commentId, $sortOrder = 'DESC');

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
