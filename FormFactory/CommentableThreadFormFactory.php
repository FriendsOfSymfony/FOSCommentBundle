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

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * CommentableThreadForm factory class.
 */
class CommentableThreadFormFactory implements CommentableThreadFormFactoryInterface
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
     * @param string $type
     * @param string $name
     */
    public function __construct(FormFactoryInterface $formFactory, $type, $name)
    {
        $this->formFactory = $formFactory;
        $this->type = $type;
        $this->name = $name;
    }

    /**
     * Creates a new form.
     *
     * @return FormInterface
     */
    public function createForm($name_suffix = null)
    {
        if (empty($name_suffix)) {
            $name_suffix = '';
        }
        $builder = $this->formFactory->createNamedBuilder($this->name . $name_suffix, $this->type, null, array('validation_groups' => array('OpenThread'), 'method' => 'PATCH'));


        return $builder->getForm();
    }
}
