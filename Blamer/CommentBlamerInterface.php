<?php

namespace FOS\CommentBundle\Blamer;

use FOS\CommentBundle\Model\CommentInterface;

/**
 * Gives a comment additional informations based on the context.
 * Typically, use it to assign an authenticated user to the comment
 * (see SecurityCommentBlamer implementation)
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface CommentBlamerInterface
{
    function blame(CommentInterface $comment);
}
