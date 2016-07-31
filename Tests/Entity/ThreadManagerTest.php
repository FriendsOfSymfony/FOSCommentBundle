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

use FOS\CommentBundle\Entity\ThreadManager;

/**
 * Tests the functionality provided by Entity\ThreadManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class ThreadManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $repository;
    protected $class;
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
        $this->class = 'FOS\CommentBundle\Tests\Entity\Thread';

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

    public function testAddThread()
    {
        $thread = $this->getMockBuilder('FOS\CommentBundle\Model\ThreadInterface')->getMock();

        $this->em->expects($this->once())
                ->method('persist')
                ->with($thread);

        $this->em->expects($this->once())
                ->method('flush');

        $manager = new ThreadManager($this->dispatcher, $this->em, $this->class);
        $manager->saveThread($thread);
    }

    public function testGetClass()
    {
        $manager = new ThreadManager($this->dispatcher, $this->em, $this->class);

        $this->assertEquals($this->class, $manager->getClass());
    }

    public function testFindThreadBy()
    {
        $thread = $this->getMockBuilder('FOS\CommentBundle\Model\ThreadInterface')->getMock();
        $criteria = array('id' => 'hello');

        $this->repository->expects($this->once())
                ->method('findOneBy')
                ->with($criteria)
                ->will($this->returnValue($thread));

        $manager = new ThreadManager($this->dispatcher, $this->em, $this->class);
        $result = $manager->findThreadBy($criteria);

        $this->assertEquals($thread, $result);
    }

    public function testFindAllThreads()
    {
        $thread = $this->getMockBuilder('FOS\CommentBundle\Model\ThreadInterface')->getMock();
        $threads = array($thread);

        $this->repository->expects($this->once())
            ->method('findAll')
            ->will($this->returnValue($threads));

        $manager = new ThreadManager($this->dispatcher, $this->em, $this->class);
        $result = $manager->findAllThreads();

        $this->assertEquals($threads, $result);
    }

    public function testFindThreadById()
    {
        $threadId = 'hello';
        $thread = $this->getMockBuilder('FOS\CommentBundle\Model\ThreadInterface')->getMock();

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $threadId))
            ->will($this->returnValue($thread));

        $manager = new ThreadManager($this->dispatcher, $this->em, $this->class);
        $result = $manager->findThreadById($threadId);

        $this->assertEquals($thread, $result);
    }

    public function testCreateThread()
    {
        $manager = new ThreadManager($this->dispatcher, $this->em, $this->class);
        $result = $manager->createThread();

        $this->assertInstanceOf('FOS\CommentBundle\Model\ThreadInterface', $result);
    }
}
