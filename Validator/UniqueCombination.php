<?php

namespace FOS\CommentBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueCombination extends Constraint
{
    
    public $message = 'This combination of identifiers already exists';
    public $properties = array();
    
    public function getRequiredOptions()
    {
        return array(
            'properties',
        );
    }
    
    public function validatedBy()
    {
        return 'unique_combination';
    }
    
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}
