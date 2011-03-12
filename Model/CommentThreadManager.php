<?php

namespace FOS\CommentBundle\Model;

/**
 * Abstract Comment thread Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class CommentThreadManager implements CommentThreadManagerInterface
{
    /**
     * Creates an empty comment thread instance
     *
     * @return CommentThread
     */
    function createCommentThread()
    {
        $class = $this->getClass();
        $commentThread = new $class;

        return $commentThread;
    }
}
