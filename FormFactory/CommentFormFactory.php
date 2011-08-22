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
    protected $createName;

    /**
     * @var string
     */
    protected $replyName;

    /**
     * Constructor.
     *
     * @param FormFactory $formFactory
     * @param string $type
     * @param string $createName
     * @param string $replyName
     */
    public function __construct(FormFactory $formFactory, $type, $createName, $replyName)
    {
        $this->formFactory = $formFactory;
        $this->type = $type;
        $this->createName = $createName;
        $this->replyName = $replyName;
    }

    /**
     * {@inheritDoc}
     */
    public function createCreateForm()
    {
        $builder = $this->formFactory->createNamedBuilder($this->type, $this->createName);

        return $builder->getForm();
    }

    /**
     * {@inheritDoc}
     */
    public function createReplyForm()
    {
        $builder = $this->formFactory->createNamedBuilder($this->type, $this->replyName);

        return $builder->getForm();
    }
}
