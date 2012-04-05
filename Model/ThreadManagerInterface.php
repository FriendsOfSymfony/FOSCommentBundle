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
     * @param string $id
     * @return ThreadInterface
     */
    function findThreadById($id);

    /**
     * Finds one comment thread by the given criteria
     *
     * @param array $criteria
     * @return ThreadInterface
     */
    function findThreadBy(array $criteria);

    /**
     * Finds threads by the given criteria
     *
     * @param array $criteria
     *
     * @return array of ThreadInterface
     */
    function findThreadsBy(array $criteria);

    /**
     * Finds all threads.
     *
     * @return array of ThreadInterface
     */
    function findAllThreads();

    /**
     * Creates an empty comment thread instance
     *
     * @param bool $id
     * @return Thread
     */
    function createThread($id = null);

    /**
     * Saves a thread
     *
     * @param ThreadInterface $thread
     */
    function saveThread(ThreadInterface $thread);

    /**
     * Returns the comment thread fully qualified class name
     *
     * @return string
     */
    function getClass();
}
