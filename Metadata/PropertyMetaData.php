<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Metadata;

use Metadata\PropertyMetadata as BasePropertyMetadata;

class PropertyMetadata extends BasePropertyMetadata
{
	public $commentable;

	public function serialize()
	{
		return serialize(array(
				$this->class,
				$this->name,
				$this->$commentable,
		));
	}

	public function unserialize($str)
	{
		list($this->class, $this->name, $this->$commentable) = unserialize($str);

		$this->reflection = new \ReflectionProperty($this->class, $this->name);
		$this->reflection->setAccessible(true);
	}
}