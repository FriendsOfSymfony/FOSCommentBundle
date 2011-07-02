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

use FOS\CommentBundle\Entity\VoteManager;

/**
 * Tests the functionality provided by Entity\VoteManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class VoteManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $em;
    protected $repository;
    protected $class;
    protected $classMetadata;

    public function setUp()
    {
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

        $this->classMetadata = new \StdClass();
        $this->classMetadata->name = $this->class;

        $this->em->expects($this->once())
            ->method('getClassMetadata')
            ->with($this->class)
            ->will($this->returnValue($this->classMetadata));
    }

    public function testGetClass()
    {
        $manager = new VoteManager($this->em, $this->class);

        $this->assertEquals($this->class, $manager->getClass());
    }

    public function testAddVote()
    {
        $voteValue = 1;
        $vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');
        $comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');

        $vote->expects($this->once())
            ->method('setComment')
            ->with($comment);

        $vote->expects($this->once())
            ->method('getValue')
            ->will($this->returnValue($voteValue));

        $comment->expects($this->once())
            ->method('incrementScore')
            ->with($voteValue);

        $this->em->expects($this->exactly(2))
            ->method('persist');

        $this->em->expects($this->once())
            ->method('flush');

        $manager = new VoteManager($this->em, $this->class);
        $manager->addVote($vote, $comment);
    }

    public function testFindVoteBy()
    {
        $vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');
        $criteria = array('id' => 123);

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with($criteria)
            ->will($this->returnValue($vote));

        $manager = new VoteManager($this->em, $this->class);
        $result = $manager->findVoteBy($criteria);

        $this->assertEquals($vote, $result);
    }

    public function testFindVoteById()
    {
        $id = 123;
        $vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');

        $this->repository->expects($this->once())
            ->method('findOneBy')
            ->with(array('id' => $id))
            ->will($this->returnValue($vote));

        $manager = new VoteManager($this->em, $this->class);
        $result = $manager->findVoteById($id);

        $this->assertEquals($vote, $result);
    }

    public function testCreateVote()
    {
        $manager = new VoteManager($this->em, $this->class);
        $result = $manager->createVote();

        $this->assertInstanceOf('FOS\CommentBundle\Model\VoteInterface', $result);
    }
}