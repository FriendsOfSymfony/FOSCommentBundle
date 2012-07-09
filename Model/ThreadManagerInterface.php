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
     * @param  string          $id
     * @return ThreadInterface
     */
    public function findThreadById($id);

    /**
     * Finds one comment thread by the given criteria
     *
     * @param  array           $criteria
     * @return ThreadInterface
     */
    public function findThreadBy(array $criteria);

    /**
     * Finds threads by the given criteria
     *
     * @param array $criteria
     *
     * @return array of ThreadInterface
     */
    public function findThreadsBy(array $criteria);

    /**
     * Finds all threads.
     *
     * @return array of ThreadInterface
     */
    public function findAllThreads();

    /**
     * Creates an empty comment thread instance
     *
     * @param  bool   $id
     * @return Thread
     */
    public function createThread($id = null);

    /**
     * Saves a thread
     *
     * @param ThreadInterface $thread
     */
    public function saveThread(ThreadInterface $thread);

    /**
     * Checks if the thread was already persisted before, or if it's a new one.
     *
     * @param ThreadInterface $thread
     *
     * @return boolean True, if it's a new thread
     */
    public function isNewThread(ThreadInterface $thread);

    /**
     * Returns the comment thread fully qualified class name
     *
     * @return string
     */
    public function getClass();
}
