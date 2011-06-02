<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Model\CommentInterface;

/**
 * Responsible for primary creation and persistence of a Comment object.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
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
