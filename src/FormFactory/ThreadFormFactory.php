<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\FormFactory;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * ThreadForm factory class.
 */
final class ThreadFormFactory implements ThreadFormFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    public function __construct(FormFactoryInterface $formFactory, string $type, string $name)
    {
        $this->formFactory = $formFactory;
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function createForm(): FormInterface
    {
        $builder = $this->formFactory->createNamedBuilder($this->name, $this->type, null, ['validation_groups' => ['CreateThread']]);

        return $builder->getForm();
    }
}
