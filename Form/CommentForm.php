<?php

/**
 * This file is part of the FOS\CommentBundle.
 *
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextareaField;

/**
 * Form for creation of a comment.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class CommentForm extends Form
{
    /**
     * Configures the form, adding appropriate fields.
     *
     * @return void
     */
    public function configure()
    {
        $this->add(new TextareaField('body'));
    }
}
