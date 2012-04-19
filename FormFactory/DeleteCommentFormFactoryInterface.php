<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\FormFactory;

use Symfony\Component\Form\FormInterface;

/**
 * DeleteComment form creator
 */
interface DeleteCommentFormFactoryInterface
{
    /**
     * Creates a delete comment form
     *
     * @return FormInterface
     */
    public function createForm();
}
