<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentableThreadType extends AbstractType
{
    private $threadClass;

    public function __construct(string $threadClass)
    {
        $this->threadClass = $threadClass;
    }

    /**
     * Configures a form to close a thread.
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isCommentable', HiddenType::class, [
            'property_path' => 'commentable',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => $this->threadClass,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'fos_comment_commentable_thread';
    }
}
