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
 * Interface to be implemented by comment managers.
 *
 * This adds an additional level of abstraction between your application, and the actual repository.
 *
 * All changes to comments should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface CommentManagerInterface
{
    /**
     * Returns a flat array of comments from the specified thread.
     *
     * The sorter parameter should be left alone if you are sorting in the
     * tree methods.
     *
     * @param ThreadInterface $thread
     * @param integer|null    $depth
     * @param string|null     $sorterAlias
     *
     * @return CommentInterface[] An array of commentInterfaces
     */
    public function findCommentsByThread(ThreadInterface $thread, $depth = null, $sorterAlias = null);

    /**
     * Returns all thread comments in a nested array.
     *
     * Will typically be used when it comes to display the comments.
     *
     * Will query for an additional level of depth when provided
     * so templates can determine to display a 'load more comments' link.
     *
     * @param ThreadInterface $thread      The thread for whom we want to find comments for.
     * @param string|null     $sorterAlias Optional name of the sorter to use.
     * @param integer|null    $depth       The depth
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
    public function findCommentTreeByThread(ThreadInterface $thread, $sorterAlias = null, $depth = null);

    /**
     * Returns a partial comment tree based on a specific parent commentId.
     *
     * @param mixed       $commentId   The unique comment identifier
     * @param string|null $sorterAlias Name of the optional sorter
     *
     * @return array See findCommentTreeByThread()
     */
    public function findCommentTreeByCommentId($commentId, $sorterAlias = null);

    /**
     * Saves a comment to the persistence backend used.
     *
     * @param CommentInterface $comment
     */
    public function saveComment(CommentInterface $comment);

    /**
     * Finds a comment by it's unique id.
     *
     * @param mixed $id The unique comment identifier.
     *
     * @return CommentInterface|null The comment or null when no comment found
     */
    public function findCommentById($id);

    /**
     * Creates a new comment object.
     *
     * @param ThreadInterface       $thread A thread instance
     * @param CommentInterface|null $parent The parent comment or null if no parent comment
     *
     * @return CommentInterface The created comment.
     */
    public function createComment(ThreadInterface $thread, CommentInterface $parent = null);

    /**
     * Checks if the comment was already persisted before, or if it's a new one.
     *
     * @param CommentInterface $comment
     *
     * @return boolean true if it's a new comment, false otherwise
     */
    public function isNewComment(CommentInterface $comment);

    /**
     * Returns the fully qualified comment class name
     *
     * @return string
     */
    public function getClass();
}
