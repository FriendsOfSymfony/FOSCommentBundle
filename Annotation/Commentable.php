<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Annotation;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Commentable
{
	/**
	 * @var $fetch Enum
	 */
    public $fetch = 'EAGER';

    /**
     * @var $identifierProperty string
     */
    public $identifierProperty = 'entityIdentifier';
}
