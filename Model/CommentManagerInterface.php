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
    /**
     * Creates an empty comment instance
     *
     * @return Comment
     */
    function createComment();
}
