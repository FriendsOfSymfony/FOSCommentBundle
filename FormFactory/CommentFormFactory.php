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

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactory;
use FOS\CommentBundle\Form\ValueTransformer\ThreadValueTransformer;

/**
 * CommentForm factory class.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentFormFactory implements CommentFormFactoryInterface
{
    /**
     * @var FormFactory
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
     * @param FormContext $formContext
     * @param string $type
     * @param string $name
     */
    public function __construct(FormFactory $formFactory, $type, $name)
    {
        $this->formFactory            = $formFactory;
        $this->type                   = $type;
        $this->name                   = $name;
    }

    /**
     * Creates a new form.
     *
     * @return Form
     */
    public function createForm()
    {
        $builder = $this->formFactory->createNamedBuilder($this->type, $this->name);

        return $builder->getForm();
    }
}
