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
interface ThreadManagerInterface
{
    /**
     * @param string $identifier
     * @return ThreadInterface
     */
    function findThreadByIdentifier($identifier);

    /**
     * Adds a comment in a thread
     *
     * @param ThreadInterface $commentThread
     * @param CommentInterface $comment
     * @param CommentInterface $parent Only used when replying to a specific CommentInterface
     */
    function addComment(ThreadInterface $commentThread, CommentInterface $comment, CommentInterface $parent = null);

    /**
     * Finds one comment thread by the given criteria
     *
     * @param array $criteria
     * @return ThreadInterface
     */
    function findThreadBy(array $criteria);

    /**
     * Creates an empty comment thread instance
     *
     * @return Thread
     */
    function createThread();

    /**
     * Returns the comment thread fully qualified class name
     *
     * @return string
     */
    function getClass();
}
