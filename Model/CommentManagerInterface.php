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
 * Interface to be implemented by comment managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface CommentManagerInterface
{
    /**
     * Returns a flat array of comments with the specified thread.
     *
     * The sorter parameter should be left alone if you are sorting in the
     * tree methods.
     *
     * @param ThreadInterface $thread
     * @param integer $depth
     * @param string $sorterAlias
     * @return array of CommentInterface
     */
    function findCommentsByThread(ThreadInterface $thread, $depth = null, $sorterAlias = null);

    /*
     * Returns all thread comments in a nested array
     * Will typically be used when it comes to display the comments.
     *
     * Will query for an additional level of depth when provided
     * so templates can determine to display a 'load more comments' link.
     *
     * @param  ThreadInterface $thread
     * @param  string $sorter The sorter to use
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
    function findCommentTreeByThread(ThreadInterface $thread, $sorter = null, $depth = null);

    /**
     * Returns a partial comment tree based on a specific parent commentId.
     *
     * @param integer $commentId
     * @param string $sorter The sorter to use
     * @return array see findCommentTreeByThread()
     */
    function findCommentTreeByCommentId($commentId, $sorter = null);

    /**
     * Saves a comment.
     *
     * @param CommentInterface $comment
     */
    function saveComment(CommentInterface $comment);

    /**
     * Find one comment by its ID.
     *
     * @return Comment or null
     */
    function findCommentById($id);

    /**
     * Creates an empty comment instance.
     *
     * @return Comment
     */
    function createComment(ThreadInterface $thread, CommentInterface $comment = null);

    /**
     * Returns the comment fully qualified class name.
     *
     * @return string
     */
    function getClass();
}
