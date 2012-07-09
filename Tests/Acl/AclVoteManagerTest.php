<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Acl;

use FOS\CommentBundle\Acl\AclVoteManager;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Tests the functionality provided by Acl\AclVoteManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class AclVoteManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $realManager;
    protected $voteSecurity;
    protected $commentSecurity;
    protected $vote;
    protected $comment;

    public function setUp()
    {
        $this->realManager = $this->getMock('FOS\CommentBundle\Model\VoteManagerInterface');
        $this->voteSecurity = $this->getMock('FOS\CommentBundle\Acl\VoteAclInterface');
        $this->commentSecurity = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $this->comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');
        $this->vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');
        $this->vote->expects($this->any())
            ->method('getComment')
            ->will($this->returnValue($this->comment));
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindVoteById()
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

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindVoteBy()
    {
        $conditions = array('id' => 1);
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
        $conditions = array('id' => 1);
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

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindVotesByComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');
        $expectedResult = array($this->vote);

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
        $comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');
        $expectedResult = array($this->vote);

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

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testAddVoteNoCreate()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');

        $this->realManager->expects($this->never())
            ->method('saveVote');

        $this->voteSecurity->expects($this->once())
            ->method('canCreate')
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->saveVote($this->vote, $comment);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testAddVoteNoViewComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');

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
        $comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');

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

        $this->assertEquals($class, $result);
    }

    public function testCreateVote()
    {
        $this->realManager->expects($this->once())
            ->method('createVote')
            ->with($this->comment)
            ->will($this->returnValue($this->vote));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $result = $manager->createVote($this->comment);

        $this->assertEquals($this->vote, $result);
    }
}
