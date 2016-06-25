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

use FOS\CommentBundle\Entity\FlagManager;
use FOS\CommentBundle\Entity\VoteManager;

/**
 * Tests the functionality provided by Entity\FlagManager.
 *
 * @author Hubert Bryłkowski <hubert@brylkowski.com>
 */
class FlagManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $dispatcher;
    protected $em;
    protected $repository;
    protected $class;
    protected $classMetadata;

    public function setUp()
    {
        if (!class_exists('Doctrine\\ORM\\EntityManager')) {
            $this->markTestSkipped('Doctrine ORM not installed');
        }

        $this->dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->class = 'FOS\CommentBundle\Tests\Entity\Flag';

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

    public function testGetClass()
    {
        $manager = new VoteManager($this->dispatcher, $this->em, $this->class);

        $this->assertEquals($this->class, $manager->getClass());
    }

    public function testAddFlag()
    {
        $flag = $this->getMock('FOS\CommentBundle\Model\FlagInterface');
        $flag->expects($this->any())
            ->method('getComment')
            ->will($this->returnValue($this->getMock('FOS\CommentBundle\Model\FlaggableCommentInterface')));

        $this->em->expects($this->exactly(2))
            ->method('persist');

        $this->em->expects($this->once())
            ->method('flush');

        $manager = new FlagManager($this->dispatcher, $this->em, $this->class);
        $manager->saveFlag($flag);
    }

    public function testFindVoteBy()
    {
        $flag = $this->getMock('FOS\CommentBundle\Model\FlagInterface');
        $criteria = array('id' => 123);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->will($this->returnValue($flag));

        $manager = new FlagManager($this->dispatcher, $this->em, $this->class);
        $result = $manager->findFlagBy($criteria);

        $this->assertEquals($flag, $result);
    }

    public function testFindVoteById()
    {
        $id = 123;
        $flag = $this->getMock('FOS\CommentBundle\Model\FlagInterface');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(['id' => $id])
            ->will($this->returnValue($flag));

        $manager = new FlagManager($this->dispatcher, $this->em, $this->class);
        $result = $manager->findFlagById($id);

        $this->assertEquals($flag, $result);
    }

    public function testCreateFlag()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\FlaggableCommentInterface');

        $manager = new FlagManager($this->dispatcher, $this->em, $this->class);
        $result = $manager->createFlag($comment);

        $this->assertInstanceOf('FOS\CommentBundle\Model\FlagInterface', $result);
    }
}
