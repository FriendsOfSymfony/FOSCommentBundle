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
    const FORM_CREATE = 1;
    const FORM_REPLY  = 2;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var array Holds type and name of comment creation form
     */
    protected $createForm;

    /**
     * @var array Holds type and name of comment reply form
     */
    protected $replyForm;

    /**
     * Constructor.
     *
     * @param FormContext $formContext
     * @param string $type
     * @param string $name
     */
    public function __construct(FormFactory $formFactory, array $createForm, array $replyForm)
    {
        $this->formFactory = $formFactory;
        $this->createForm  = $createForm;
        $this->replyForm   = $replyForm;
    }

    /**
     * Creates a new form.
     *
     * @return Form
     */
    public function createForm($type)
    {
        if (self::FORM_CREATE === $type) {
            $builder = $this->formFactory->createNamedBuilder($this->createForm['type'], $this->createForm['name']);
        } else {
            $builder = $this->formFactory->createNamedBuilder($this->replyForm['type'], $this->replyForm['name']);
        }

        return $builder->getForm();
    }
}
