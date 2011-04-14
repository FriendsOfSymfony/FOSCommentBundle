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

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormContext;
use Symfony\Component\Form\HiddenField;
use FOS\CommentBundle\Form\ValueTransformer\ThreadValueTransformer;

/**
 * CommentForm factory class.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class CommentFormFactory implements CommentFormFactoryInterface
{
    /**
     * @var FormContext
     */
    protected $formContext;

    /**
     * @var ThreadValueTransformer
     */
    protected $threadValueTransformer;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $name;

    /**
     * Constructor.
     *
     * @param FormContext $formContext
     * @param ThreadValueTransformer $threadValueTransformer
     * @param string $class
     * @param string $name
     */
    public function __construct(FormContext $formContext, ThreadValueTransformer $threadValueTransformer, $class, $name)
    {
        $this->formContext            = $formContext;
        $this->threadValueTransformer = $threadValueTransformer;
        $this->class                  = $class;
        $this->name                   = $name;
    }

    /**
     * Creates a new form.
     *
     * @return Form
     */
    public function createForm()
    {
        $class = $this->class;
        $form = $class::create($this->formContext, $this->name);
        $form->add(new HiddenField('thread', array('value_transformer' => $this->threadValueTransformer)));

        return $form;
    }
}
