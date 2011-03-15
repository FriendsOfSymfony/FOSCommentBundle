<?php

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Model\CommentInterface;

interface CommentCreatorInterface
{
    /**
     * Creates and saves a comment from the request
     * Should blame the comment and test it against spam
     *
     * @param CommentInterface $comment
     * @return bool whether the comment was successfuly created or not
     */
    function create(CommentInterface $comment);
}
