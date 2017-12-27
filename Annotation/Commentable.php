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

use Doctrine\ORM\Mapping\ClassMetadataInfo;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Commentable
{

	private $defaults =  array(
		                    'targetEntity' => NULL,
		                    'fieldName' => NULL,
		                    'cascade' => array('persist'),
		                    'fetch' => ClassMetadataInfo::FETCH_LAZY,
		                    'joinColumn' => array(
						    	'name' => 'comment_thread_id',
						        'referencedColumnName' => 'id'
		                    )
						);
	public $value;

	public function __construct(array $data){
		$this->value = array_merge($this->defaults, $data);
	}
}
