<?php

namespace FOS\CommentBundle\FormFactory;

use FOS\CommentBundle\Form\CommentForm;

interface CommentFormFactoryInterface
{
    /**
     * Creates a comment form
     *
     * @return CommentForm
     */
    public function createForm();
}
