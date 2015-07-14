<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\EventListener;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\EventArgs;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A listener that dynamically adds relations for commentable annotation
 *
 * @author Nico Kaag <nico.kaag@genj.nl>
 */
class DynamicRelationListener implements EventSubscriber
{
    /**
     * @var string $threadClass contains value of 'fos_comment.model.thread.class' parameter
     */
    private $threadClass;

    /**
     * @var Reader
     */
    private $reader;

    public function __construct($threadClass, Reader $reader)
    {
        $this->threadClass = $threadClass;

        $this->reader = $reader;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata',
            'postLoad',
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        // the $metadata is the whole mapping info for this class
        $metadata = $eventArgs->getClassMetadata();

        if (!$metadata->getReflectionClass()) {
            return;
        }

        $properties = $this->getCommentableProperties($metadata->getReflectionClass());

        if (empty($properties)) {
            return;
        }

        $namingStrategy = $eventArgs
            ->getEntityManager()
            ->getConfiguration()
            ->getNamingStrategy()
        ;

        foreach ($properties as $property) {
            $metadata->mapOneToOne(
                array(
                    'targetEntity' => $this->threadClass,
                    'fieldName' => $property['property'],
                    'cascade' => array('persist'),
                    'fetch' => ClassMetadataInfo::FETCH_LAZY,
                    'joinColumn' => array(
                        'name' => 'comment_thread_id',
                        'referencedColumnName' => 'id'
                    ),
                )
            );
        }
    }

    public function postLoad(EventArgs $args)
    {
        $this->addCommentThreadToObject($args);
    }

    private function addCommentThreadToObject(EventArgs $args)
    {
        $om = $args->getObjectManager();
        $object = $args->getObject();
        if(!is_object($object)) return;

        $reflectionClass = new \ReflectionClass($object);
        $properties = $this->getCommentableProperties($reflectionClass);
        if (empty($properties)) return;


        foreach ($properties as $property) {
            $commentThreadGetter = 'get'. ucfirst($property['property']);
            $commentThread = $object->$commentThreadGetter();

            if (!empty($commentThread)) {
                return;
            }

            $threadClass = $this->threadClass;
            $commentThread = new $threadClass();

            $entityIdentifierMethod = 'get' . ucfirst($property['config']->identifierProperty);

            $commentThread->setId($object->$entityIdentifierMethod());
            $commentThread->setPermaLink('');

            $commentThreadSetter = 'set'. ucfirst($property['property']);
            $object->$commentThreadSetter($commentThread);

            $om->persist($object);
        }
        $om->flush();
    }

    private function getCommentableProperties(\ReflectionClass $reflectedObj)
    {
        // get all properties with their reflections
        $properties = $reflectedObj->getProperties();

        $commentableProperties = array();

        // iterate over all properties to check for the @Annotation
        foreach ($properties as $prop) {
            $annotation = $this->reader->getPropertyAnnotation(
                $prop,
                'FOS\CommentBundle\Annotation\Commentable'
            );
            // get the name of the property/field
            if (!empty($annotation)) {
                $property = $prop->getName();

                $commentableProperties[] = array('property' => $property, 'config' => $annotation);
            }
        }

        return $commentableProperties;
    }
}
