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

use FOS\CommentBundle\Model\FlaggableCommentInterface;
use FOS\CommentBundle\Util\LegacyFormHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FlagType extends AbstractType
{
    private $flagClass;

    public function __construct($flagClass)
    {
        $this->flagClass = $flagClass;
    }

    /**
     * Configures a Comment form.
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('reason', LegacyFormHelper::getType(TextareaType::class));
        $builder->add('type', LegacyFormHelper::getType(ChoiceType::class), ['choices' => [
            FlaggableCommentInterface::FLAG_ABUSIVE => 'fos_comment_flag_abusive',
            FlaggableCommentInterface::FLAG_INAPPROPRIATE=> 'fos_comment_flag_inappropriate',
            FlaggableCommentInterface::FLAG_SPAM => 'fos_comment_flag_spam',
        ]]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $this->configureOptions($resolver);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                                   'data_class' => $this->flagClass,
        ));
    }

    public function getBlockPrefix()
    {
        return 'fos_comment_flag';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
