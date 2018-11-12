<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Entity;

use FOS\CommentBundle\Entity\VoteManager;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality provided by Entity\VoteManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class VoteManagerTest extends TestCase
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

        $this->dispatcher = $this->getMockBuilder('Symfony\Component\EventDispatcher\EventDispatcherInterface')->getMock();
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();
        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->class = 'FOS\CommentBundle\Tests\Entity\Vote';

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

        $this->assertSame($this->class, $manager->getClass());
    }

    public function testAddVote()
    {
        // @todo uncomment this in 3.0 and remove the abstract class.
        // $vote = $this->getMockBuilder('FOS\CommentBundle\Model\VoteInterface')->getMock();
        $vote = $this->getMockForAbstractClass('FOS\CommentBundle\Tests\Fixtures\AbstractVote');
        $vote->expects($this->any())
            ->method('getComment')
            ->will($this->returnValue($this->getMockBuilder('FOS\CommentBundle\Model\VotableCommentInterface')->getMock()));

        $this->em->expects($this->exactly(2))
            ->method('persist');

        $this->em->expects($this->once())
            ->method('flush');

        $manager = new VoteManager($this->dispatcher, $this->em, $this->class);
        $manager->saveVote($vote);
    }

    public function testFindVoteBy()
    {
        // @todo uncomment this in 3.0 and remove the abstract class.
        // $vote = $this->getMockBuilder('FOS\CommentBundle\Model\VoteInterface')->getMock();
        $vote = $this->getMockForAbstractClass('FOS\CommentBundle\Tests\Fixtures\AbstractVote');
        $criteria = array('id' => 123);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->will($this->returnValue($vote));

        $manager = new VoteManager($this->dispatcher, $this->em, $this->class);
        $result = $manager->findVoteBy($criteria);

        $this->assertSame($vote, $result);
    }

    public function testFindVoteById()
    {
        $id = 123;
        // @todo uncomment this in 3.0 and remove the abstract class.
        // $vote = $this->getMockBuilder('FOS\CommentBundle\Model\VoteInterface')->getMock();
        $vote = $this->getMockForAbstractClass('FOS\CommentBundle\Tests\Fixtures\AbstractVote');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $id))
            ->will($this->returnValue($vote));

        $manager = new VoteManager($this->dispatcher, $this->em, $this->class);
        $result = $manager->findVoteById($id);

        $this->assertSame($vote, $result);
    }

    public function testCreateVote()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\VotableCommentInterface')->getMock();

        $manager = new VoteManager($this->dispatcher, $this->em, $this->class);
        $result = $manager->createVote($comment);

        $this->assertInstanceOf('FOS\CommentBundle\Model\VoteInterface', $result);
    }

    public function testRemoveVote()
    {
        $vote = $this->getMockForAbstractClass('FOS\CommentBundle\Tests\Fixtures\AbstractVote');

        $this->em->expects($this->once())
            ->method('persist');

        $this->em->expects($this->once())
            ->method('remove');

        $this->em->expects($this->once())
            ->method('flush');

        $manager = new VoteManager($this->dispatcher, $this->em, $this->class);
        $manager->removeVote($vote);
    }
}
