<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

abstract class AbstractCommentType extends AbstractType
{
    /**
     * Configures a Comment form.
     *
     * @param FormBuilder $builder
     * @param array $options
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('body', 'textarea');
    }

    public function getName()
    {
        preg_match('/\\\\(\w+)CommentType$/i', get_class($this), $matches);
        return 'fos_comment_comment_' . strtolower($matches[1]);
    }
}
