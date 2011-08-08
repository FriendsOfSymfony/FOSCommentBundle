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

    public function setUp()
    {
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

        $this->classMetadata = new \StdClass();
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

        $commentManager = new CommentManager($this->em, $this->class, $this->sortingFactory);
        $commentManager->findCommentById($commentId);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddCommentAlreadySaved()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $comment->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(1));

        $commentManager = new CommentManager($this->em, $this->class, $this->sortingFactory);
        $commentManager->addComment($comment);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddCommentNoThread()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $comment->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));

        $comment->expects($this->once())
            ->method('getThread')
            ->will($this->returnValue(null));

        $commentManager = new CommentManager($this->em, $this->class, $this->sortingFactory);
        $commentManager->addComment($comment);
    }

    public function testAddComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $comment->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(null));

        $thread = $this->getMock('FOS\CommentBundle\Model\ThreadInterface');
        $comment->expects($this->any())
            ->method('getThread')
            ->will($this->returnValue($thread));

        // TODO: Not sure how to set the assertion that this method
        // will be called twice with different parameters.
        $this->em->expects($this->exactly(2))
            ->method('persist');

        $this->em->expects($this->once())
            ->method('flush');

        $commentManager = new CommentManager($this->em, $this->class, $this->sortingFactory);
        $commentManager->addComment($comment);
    }

    public function testGetClass()
    {
        $commentManager = new CommentManager($this->em, $this->class, $this->sortingFactory);

        $this->assertEquals($this->class, $commentManager->getClass());
    }

    public function testCreateComment()
    {
        $thread = $this->getMock('FOS\CommentBundle\Entity\Thread');
        $parent = $this->getMock('FOS\CommentBundle\Entity\Comment');

        $parent->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $manager = new CommentManager($this->em, $this->class, $this->sortingFactory);
        $result = $manager->createComment($thread, $parent);

        $this->assertInstanceOf('FOS\CommentBundle\Model\CommentInterface', $result);
        $this->assertEquals($thread, $result->getThread());
        $this->assertEquals($parent, $result->getParent());
    }
}