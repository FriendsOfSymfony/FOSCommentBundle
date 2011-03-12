<?php

namespace FOS\CommentBundle\Model;

/**
 * Interface to be implemented by comment thread managers. This adds an additional level
 * of abstraction between your application, and the actual repository.
 *
 * All changes to comment threads should happen through this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface CommentThreadManagerInterface
{
    /**
     * @param string $identifier
     * @return CommentThreadInterface
     */
    function findThreadByIdentifier($identifier);

    /**
     * Adds a comment in a thread
     *
     * @param CommentThreadInterface $commentThread
     * @param CommentInterface $comment
     * @param CommentInterface $parent Only used when replying to a specific CommentInterface
     */
    function addComment(CommentThreadInterface $commentThread, CommentInterface $comment, CommentInterface $parent = null);

    /**
     * Creates an empty comment thread instance
     *
     * @return CommentThread
     */
    function createCommentThread();

    /**
     * Returns the comment thread fully qualified class name
     *
     * @return string
     */
    function getClass();
}
