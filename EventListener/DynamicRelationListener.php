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
use Symfony\Component\DependencyInjection\Container;

/**
 * A listener that dynamically adds relations for commentable annotation
 *
 * @author Nico Kaag <nico.kaag@genj.nl>
 */
class DynamicRelationListener implements EventSubscriber
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var AnnotationReader
     */
    protected $reader;

    public function __construct(Container $container, Reader $reader) {
        $this->container = $container;

        $this->reader = $reader;
    }

    /**
     * {@inheritDoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            \Doctrine\ORM\Events::loadClassMetadata,
            \Doctrine\ORM\Events::postLoad,

        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        // the $metadata is the whole mapping info for this class
        $metadata = $eventArgs->getClassMetadata();

        $properties = $this->getCommentableProperties($metadata->getReflectionClass());

        if (empty($properties)) {
            return;
        }

        $namingStrategy = $eventArgs
            ->getEntityManager()
            ->getConfiguration()
            ->getNamingStrategy()
        ;

        $threadClass = $this->container->getParameter('fos_comment.model.thread.class');

        foreach ($properties as $property) {
            $metadata->mapOneToOne(
                array(
                    'targetEntity' => $threadClass,
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

    protected function addCommentThreadToObject(EventArgs $args) {

        $om = $args->getObjectManager();
        $object = $args->getObject();

        $properties = $this->getCommentableProperties(new \ReflectionClass($object));

        if (empty($properties)) {
            return;
        }

        foreach ($properties as $property) {
            $commentThreadGetter = "get". ucfirst($property['property']);
            $commentThread = $object->$commentThreadGetter();

            if (!empty($commentThread)) {
                return;
            }

            $threadClass = $this->container->getParameter('fos_comment.model.thread.class');
            $commentThread = new $threadClass();

            $entityIdentifierMethod = "get" . ucfirst($property['config']->identifierProperty);

            $commentThread->setId($object->$entityIdentifierMethod());
            $commentThread->setPermaLink('');

            $commentThreadSetter = "set". ucfirst($property['property']);
            $object->$commentThreadSetter($commentThread);

            $om->persist($object);
        }
        $om->flush();
    }

    protected function getCommentableProperties(\ReflectionClass $reflectionProperties) {
        // get all properties with their reflections
        $properties = $reflectionProperties->getProperties();

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
