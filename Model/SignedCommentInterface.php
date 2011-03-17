<?php

namespace FOS\CommentBundle\Model;

use FOS\UserBundle\Model\UserInterface;

/**
 * A signed comment is bound to a FOS\UserBundle User model.
 */
interface SignedCommentInterface
{
    /**
     * Sets the author of the Comment
     *
     * @return UserInterface
     */
    function getAuthor();

    /**
     * Sets the author of the Comment
     *
     * @param UserInterface $user
     */
    function setAuthor($author);
}
