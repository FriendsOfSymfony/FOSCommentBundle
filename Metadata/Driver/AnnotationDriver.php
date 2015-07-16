<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Metadata\Driver;

use Metadata\Driver\DriverInterface;
use Metadata\MergeableClassMetadata;
use Doctrine\Common\Annotations\Reader;
use FOS\CommentBundle\Metadata\PropertyMetadata;

class AnnotationDriver implements DriverInterface
{
	private $reader;

	public function __construct(Reader $reader)
	{
		$this->reader = $reader;
	}

	public function loadMetadataForClass(\ReflectionClass $class)
	{
		$className = $class->getName();
		$classMetadata = new MergeableClassMetadata($className);

		foreach ($class->getProperties() as $reflectionProperty) {
			$propName = $reflectionProperty->getName();
			$propertyMetadata = new PropertyMetadata($className, $propName);

			$annotation = $this->reader->getPropertyAnnotation(
					$reflectionProperty,
					'FOS\\CommentBundle\\Annotation\\Commentable'
					);

			if (null !== $annotation) {
				// a "@Commentable" annotation was found
				$annotation->value['targetEntity'] = $className;
				$annotation->value['fieldName'] = $propName;

				$propertyMetadata->commentable = $annotation->value;

				$classMetadata->addPropertyMetadata($propertyMetadata);
			}


		}

		return $classMetadata;
	}
}