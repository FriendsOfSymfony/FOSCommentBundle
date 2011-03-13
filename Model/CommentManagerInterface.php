<?php

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
    function findCommentsByThreadIdentifier($threadIdentifier);

    /**
     * Adds a comment in a thread
     *
     * @param ThreadInterface $commentThread
     * @param CommentInterface $comment
     * @param CommentInterface $parent Only used when replying to a specific CommentInterface
     */
    function addComment(ThreadInterface $commentThread, CommentInterface $comment, CommentInterface $parent = null);

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
