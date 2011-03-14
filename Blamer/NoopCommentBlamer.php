<?php

namespace FOS\CommentBundle\Blamer;

use FOS\CommentBundle\Model\CommentInterface;

/**
 * Does not blame a comment.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NoopCommentBlamer implements CommentBlamerInterface
{
    public function blame(CommentInterface $comment)
    {
        // Do nothing.
    }
}
