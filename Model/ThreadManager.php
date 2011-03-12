<?php

namespace FOS\CommentBundle\Model;

/**
 * Abstract Comment thread Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class ThreadManager implements ThreadManagerInterface
{
    /**
     * @param string $identifier
     * @return ThreadInterface
     */
    public function findThreadByIdentifier($identifier)
    {
        return $this->findThreadBy(array('identifier' => $identifier));
    }

    /**
     * Creates an empty comment thread instance
     *
     * @return Thread
     */
    function createThread()
    {
        $class = $this->getClass();
        $commentThread = new $class;

        return $commentThread;
    }
}
