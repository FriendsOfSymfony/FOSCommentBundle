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
 * Gives a comment additional information based on the context.
 * Typically, use it to assign an authenticated user to the comment
 * (see SecurityCommentBlamer implementation)
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface CommentBlamerInterface
{
    /**
     * Assigns additional information to a comment.
     *
     * @param CommentInterface $comment
     * @return void
     */
    function blame(CommentInterface $comment);
}
