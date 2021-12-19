<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Acl;

use FOS\CommentBundle\Acl\AclVoteManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Tests the functionality provided by Acl\AclVoteManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class AclVoteManagerTest extends TestCase
{
    protected $realManager;
    protected $voteSecurity;
    protected $commentSecurity;
    protected $vote;
    protected $comment;

    public function setUp(): void
    {
        $this->realManager = $this->getMockBuilder('FOS\CommentBundle\Model\VoteManagerInterface')->getMock();
        $this->voteSecurity = $this->getMockBuilder('FOS\CommentBundle\Acl\VoteAclInterface')->getMock();
        $this->commentSecurity = $this->getMockBuilder('FOS\CommentBundle\Acl\CommentAclInterface')->getMock();
        $this->comment = $this->getMockBuilder('FOS\CommentBundle\Model\VotableCommentInterface')->getMock();
        $this->vote = $this->getMockBuilder('FOS\CommentBundle\Model\VoteInterface')->getMock();
        $this->vote->expects($this->any())
            ->method('getComment')
            ->will($this->returnValue($this->comment));
    }

    public function testFindVoteById()
    {
        self::expectException(AccessDeniedException::class);

        $id = 1;
        $expectedResult = $this->vote;

        $this->realManager->expects($this->once())
            ->method('findVoteById')
            ->with($id)
            ->will($this->returnValue($expectedResult));

        $this->voteSecurity->expects($this->once())
            ->method('canView')
            ->with($this->vote)
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->findVoteById($id);
    }

    public function testFindVoteByIdAllowed()
    {
        $id = 1;
        $expectedResult = $this->vote;

        $this->realManager->expects($this->once())
            ->method('findVoteById')
            ->with($id)
            ->will($this->returnValue($expectedResult));

        $this->voteSecurity->expects($this->once())
            ->method('canView')
            ->with($this->vote)
            ->will($this->returnValue(true));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $result = $manager->findVoteById($id);

        $this->assertSame($expectedResult, $result);
    }

    public function testFindVoteBy()
    {
        self::expectException(AccessDeniedException::class);

        $conditions = ['id' => 1];
        $expectedResult = $this->vote;

        $this->realManager->expects($this->once())
            ->method('findVoteBy')
            ->with($conditions)
            ->will($this->returnValue($expectedResult));

        $this->voteSecurity->expects($this->once())
            ->method('canView')
            ->with($this->vote)
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->findVoteBy($conditions);
    }

    public function testFindVoteByAllowed()
    {
        $conditions = ['id' => 1];
        $expectedResult = $this->vote;

        $this->realManager->expects($this->once())
            ->method('findVoteBy')
            ->with($conditions)
            ->will($this->returnValue($expectedResult));

        $this->voteSecurity->expects($this->once())
            ->method('canView')
            ->with($this->vote)
            ->will($this->returnValue(true));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $result = $manager->findVoteBy($conditions);

        $this->assertSame($expectedResult, $result);
    }

    public function testFindVotesByComment()
    {
        self::expectException(AccessDeniedException::class);

        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\VotableCommentInterface')->getMock();
        $expectedResult = [$this->vote];

        $this->realManager->expects($this->once())
            ->method('findVotesByComment')
            ->with($comment)
            ->will($this->returnValue($expectedResult));

        $this->voteSecurity->expects($this->once())
            ->method('canView')
            ->with($this->vote)
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->findVotesByComment($comment);
    }

    public function testFindVotesByCommentAllowed()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\VotableCommentInterface')->getMock();
        $expectedResult = [$this->vote];

        $this->realManager->expects($this->once())
            ->method('findVotesByComment')
            ->with($comment)
            ->will($this->returnValue($expectedResult));

        $this->voteSecurity->expects($this->once())
            ->method('canView')
            ->with($this->vote)
            ->will($this->returnValue(true));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $result = $manager->findVotesByComment($comment);

        $this->assertSame($expectedResult, $result);
    }

    public function testAddVoteNoCreate()
    {
        self::expectException(AccessDeniedException::class);

        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\VotableCommentInterface')->getMock();

        $this->realManager->expects($this->never())
            ->method('saveVote');

        $this->voteSecurity->expects($this->once())
            ->method('canCreate')
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->saveVote($this->vote, $comment);
    }

    public function testAddVoteNoViewComment()
    {
        self::expectException(AccessDeniedException::class);

        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\VotableCommentInterface')->getMock();

        $this->realManager->expects($this->never())
            ->method('saveVote');

        $this->voteSecurity->expects($this->once())
            ->method('canCreate')
            ->will($this->returnValue(true));

        $this->commentSecurity->expects($this->once())
            ->method('canView')
            ->with($comment)
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->saveVote($this->vote, $comment);
    }

    public function testAddVote()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\VotableCommentInterface')->getMock();

        $this->realManager->expects($this->once())
            ->method('saveVote')
            ->with($this->vote);

        $this->voteSecurity->expects($this->once())
            ->method('canCreate')
            ->will($this->returnValue(true));

        $this->commentSecurity->expects($this->once())
            ->method('canView')
            ->with($comment)
            ->will($this->returnValue(true));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->saveVote($this->vote);
    }

    public function testGetClass()
    {
        $class = 'Hello\Hello';

        $this->realManager->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue($class));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $result = $manager->getClass();

        $this->assertSame($class, $result);
    }

    public function testCreateVote()
    {
        $this->realManager->expects($this->once())
            ->method('createVote')
            ->with($this->comment)
            ->will($this->returnValue($this->vote));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $result = $manager->createVote($this->comment);

        $this->assertSame($this->vote, $result);
    }
}
