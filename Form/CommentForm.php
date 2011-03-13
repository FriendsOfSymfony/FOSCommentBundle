<?php

namespace FOS\CommentBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\TextareaField;

class CommentForm extends Form
{
    public function configure()
    {
        $this->add(new TextareaField('body'));
    }
}
