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
    /**
     * Sits around doing nothing.
     *
     * @param CommentInterface $comment
     * @return void
     */
    public function blame(CommentInterface $comment)
    {
        // Do nothing.
    }
}
