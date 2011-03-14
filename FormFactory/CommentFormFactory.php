<?php

namespace FOS\CommentBundle\FormFactory;

use Symfony\Component\Form\FormContext;
use Symfony\Component\Form\HiddenField;
use FOS\CommentBundle\Form\ValueTransformer\ThreadValueTransformer;

class CommentFormFactory implements CommentFormFactoryInterface
{
    protected $formContext;
    protected $threadValueTransformer;
    protected $class;
    protected $name;

    public function __construct(FormContext $formContext, ThreadValueTransformer $threadValueTransformer, $class, $name)
    {
        $this->formContext            = $formContext;
        $this->threadValueTransformer = $threadValueTransformer;
        $this->class                  = $class;
        $this->name                   = $name;
    }

    public function createForm()
    {
        $class = $this->class;
        $form = $class::create($this->formContext, $this->name);
        $form->add(new HiddenField('thread', array('value_transformer' => $this->threadValueTransformer)));

        return $form;
    }
}
