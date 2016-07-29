<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Entity;

use FOS\CommentBundle\Entity\CommentManager;

/**
 * Tests the functionality provided by Entity\CommentManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $repository;
    protected $class;
    protected $sortingFactory;
    protected $classMetadata;
    protected $dispatcher;

    public function setUp()
    {
        if (!class_exists('Doctrine\\ORM\\EntityManager')) {
            $this->markTestSkipped('Doctrine ORM not installed');
        }

        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->class = 'FOS\CommentBundle\Tests\Entity\Comment';
        $this->sortingFactory = $this->getMockBuilder('FOS\CommentBundle\Sorting\SortingFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->repository));

        $this->classMetadata = new \stdClass();
        $this->classMetadata->name = $this->class;

        $this->em->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->class)
            ->will($this->returnValue($this->classMetadata));
    }

    public function testFindCommentById()
    {
        $commentId = 'id';

        $this->repository->expects($this->once())
            ->method('find')
            ->with($commentId);

        $commentManager = new CommentManager($this->dispatcher, $this->sortingFactory, $this->em, $this->class);
        $commentManager->findCommentById($commentId);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSaveCommentNoThread()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $comment->expects($this->once())
            ->method('getThread')
            ->will($this->returnValue(null));

        $commentManager = new CommentManager($this->dispatcher, $this->sortingFactory, $this->em, $this->class);
        $commentManager->saveComment($comment);
    }

    public function testSaveComment()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();

        $thread = $this->getMockBuilder('FOS\CommentBundle\Model\ThreadInterface')->getMock();
        $comment->expects($this->any())
            ->method('getThread')
            ->will($this->returnValue($thread));

        // TODO: Not sure how to set the assertion that this method
        // will be called twice with different parameters.
        $this->em->expects($this->exactly(2))
            ->method('persist');

        $this->em->expects($this->once())
            ->method('flush');

        $commentManager = new CommentManager($this->dispatcher, $this->sortingFactory, $this->em, $this->class);
        $commentManager->saveComment($comment);
    }

    public function testGetClass()
    {
        $commentManager = new CommentManager($this->dispatcher, $this->sortingFactory, $this->em, $this->class);

        $this->assertEquals($this->class, $commentManager->getClass());
    }

    public function testCreateComment()
    {
        $thread = $this->getMockBuilder('FOS\CommentBundle\Entity\Thread')->getMock();
        $parent = $this->getMockBuilder('FOS\CommentBundle\Entity\Comment')->getMock();

        $parent->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $manager = new CommentManager($this->dispatcher, $this->sortingFactory, $this->em, $this->class);
        $result = $manager->createComment($thread, $parent);

        $this->assertInstanceOf('FOS\CommentBundle\Model\CommentInterface', $result);
        $this->assertEquals($thread, $result->getThread());
        $this->assertEquals($parent, $result->getParent());
    }
}
