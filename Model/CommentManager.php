<?php

namespace FOS\CommentBundle\Model;

/**
 * Abstract Comment Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class CommentManager implements CommentManagerInterface
{
    /**
     * Returns an empty comment instance
     *
     * @return Comment
     */
    public function createComment()
    {
        $class = $this->getClass();
        $comment = new $class;

        return $comment;
    }
}
