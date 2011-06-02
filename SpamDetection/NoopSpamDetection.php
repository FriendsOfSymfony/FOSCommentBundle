<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\SpamDetection;

use FOS\CommentBundle\Model\CommentInterface;

/**
 * A very trusting spam detector.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class NoopSpamDetection implements SpamDetectionInterface
{
    /**
     * Returns true for all comments.
     *
     * @param CommentInterface $comment
     * @return bool
     */
    public function isSpam(CommentInterface $comment)
    {
        return false;
    }
}
