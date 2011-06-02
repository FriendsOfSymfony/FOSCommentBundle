<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
