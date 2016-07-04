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
use Symfony\Component\Form\FormFactoryInterface;

/**
 * ThreadForm factory class.
 */
class ThreadFormFactory implements ThreadFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param string               $type
     * @param string               $name
     */
    public function __construct(FormFactoryInterface $formFactory, $type, $name)
    {
        $this->formFactory = $formFactory;
        $this->type        = $type;
        $this->name        = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function createForm()
    {
        $builder = $this->formFactory->createNamedBuilder($this->name, $this->type, null, array('validation_groups' => array('CreateThread')));

        return $builder->getForm();
    }
}
