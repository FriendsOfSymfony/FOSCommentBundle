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
 * Spam detection interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface SpamDetectionInterface
{
    /**
     * Takes the comment instance and should return a boolean value
     * depending on if the Spam service thinks the comment is spam.
     *
     * @param  CommentInterface $comment
     * @return boolean
     */
    public function isSpam(CommentInterface $comment);
}
