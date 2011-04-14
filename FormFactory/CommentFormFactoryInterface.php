<?php

/**
 * This file is part of the FOS\CommentBundle.
 *
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\FormFactory;

use FOS\CommentBundle\Form\CommentForm;

interface CommentFormFactoryInterface
{
    /**
     * Creates a comment form
     *
     * @return CommentForm
     */
    public function createForm();
}
