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
 * Abstract Thread Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class ThreadManager implements ThreadManagerInterface
{
    /**
     * @param string $id
     * @return ThreadInterface
     */
    public function findThreadById($id)
    {
        return $this->findThreadBy(array('id' => $id));
    }

    /**
     * Creates an empty comment thread instance
     *
     * @return Thread
     */
    public function createThread()
    {
        $class = $this->getClass();
        $commentThread = new $class;

        return $commentThread;
    }
}
