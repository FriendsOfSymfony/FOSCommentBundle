<?php
namespace FOS\CommentBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Commentable
{
    public $fetch = 'EAGER';
    public $identifierProperty = 'entityIdentifier';
}
