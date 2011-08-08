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

use FOS\CommentBundle\Model\ThreadInterface;

/**
 * Responsible for the creation of Threads.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ThreadCreatorInterface
{
    /**
     * Creates a new thread with this id
     *
     * @param string $id
     * @return ThreadInterface
     */
    function create($id);
}
