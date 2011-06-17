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
    protected $result;
    protected $vote;

    public function setUp()
    {
        $this->realManager = $this->getMock('FOS\CommentBundle\Model\VoteManagerInterface');
        $this->voteSecurity = $this->getMock('FOS\CommentBundle\Acl\VoteAclInterface');
        $this->commentSecurity = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $this->vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');
        $this->result = null;
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindVoteById()
    {
        $id = 1;
        $this->result = $this->vote;

        $this->realManager->expects($this->once())
            ->method('findVoteById')
            ->with($id)
            ->will($this->returnValue($this->result));

        $this->voteSecurity->expects($this->once())
            ->method('canView')
            ->with($this->vote)
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->findVoteById($id);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindVoteBy()
    {
        $conditions = array('id' => 1);
        $this->result = $this->vote;

        $this->realManager->expects($this->once())
            ->method('findVoteBy')
            ->with($conditions)
            ->will($this->returnValue($this->result));

        $this->voteSecurity->expects($this->once())
            ->method('canView')
            ->with($this->vote)
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->findVoteBy($conditions);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindVotesByComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');
        $this->result = array($this->vote);

        $this->realManager->expects($this->once())
            ->method('findVotesByComment')
            ->with($comment)
            ->will($this->returnValue($this->result));

        $this->voteSecurity->expects($this->once())
            ->method('canView')
            ->with($this->vote)
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->findVotesByComment($comment);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testAddVoteNoCreate()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');

        $this->realManager->expects($this->never())
            ->method('addVote');

        $this->voteSecurity->expects($this->once())
            ->method('canCreate')
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->addVote($this->vote, $comment);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testAddVoteNoViewComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');

        $this->realManager->expects($this->never())
            ->method('addVote');

        $this->voteSecurity->expects($this->once())
            ->method('canCreate')
            ->will($this->returnValue(true));

        $this->commentSecurity->expects($this->once())
            ->method('canView')
            ->with($comment)
            ->will($this->returnValue(false));

        $manager = new AclVoteManager($this->realManager, $this->voteSecurity, $this->commentSecurity);
        $manager->addVote($this->vote, $comment);
    }
}