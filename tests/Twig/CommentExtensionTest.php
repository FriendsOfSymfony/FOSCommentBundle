<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Twig;

use FOS\CommentBundle\Twig\CommentExtension;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality provided by Twig\Extension.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentExtensionTest extends TestCase
{
    protected $extension;

    public function setUp(): void
    {
        $this->extension = new CommentExtension();
    }

    public function testIsVotableNonObject()
    {
        $this->assertFalse($this->extension->isVotable('NotAVoteObject'));
    }

    public function testIsntVotable()
    {
        $this->assertFalse($this->extension->isVotable(new \stdClass()));
    }

    public function testIsVotable()
    {
        $votableComment = $this->getMockBuilder('FOS\CommentBundle\Model\VotableCommentInterface')->getMock();
        $this->assertTrue($this->extension->isVotable($votableComment));
    }

    public function testCanCreateRootCommentWithNullAcl()
    {
        $this->assertTrue($this->extension->canComment());
    }

    public function testCanCreateRootCommentWithAcl()
    {
        $commentAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\CommentAclInterface')->getMock();
        $commentAcl->expects($this->once())->method('canCreate')->will($this->returnValue(true));
        $extension = new CommentExtension($commentAcl);
        $this->assertTrue($extension->canComment());
    }

    public function testCannotCreateCommentOnClosedThread()
    {
        $thread = $this->getMockBuilder('FOS\CommentBundle\Model\ThreadInterface')->getMock();
        $thread->expects($this->once())->method('isCommentable')->will($this->returnValue(false));
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $comment->expects($this->exactly(2))->method('getThread')->will($this->returnValue($thread));
        $extension = new CommentExtension();
        $this->assertFalse($extension->canComment($comment));
    }

    public function testCannotCreateRootCommentWithAcl()
    {
        $commentAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\CommentAclInterface')->getMock();
        $commentAcl->expects($this->once())->method('canCreate')->will($this->returnValue(false));
        $extension = new CommentExtension($commentAcl);
        $this->assertFalse($extension->canComment());
    }

    public function testAclCanReplyToComment()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $commentAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\CommentAclInterface')->getMock();
        $commentAcl->expects($this->once())->method('canReply')->with($comment)->will($this->returnValue(true));
        $extension = new CommentExtension($commentAcl);
        $this->assertTrue($extension->canComment($comment));
    }

    public function testAclCannotReplyToComment()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $commentAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\CommentAclInterface')->getMock();
        $commentAcl->expects($this->once())->method('canReply')->with($comment)->will($this->returnValue(false));
        $extension = new CommentExtension($commentAcl);
        $this->assertFalse($extension->canComment($comment));
    }

    public function testCannotVoteOnNonVotable()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $this->assertFalse($this->extension->canVote($comment));
    }

    public function testCanVoteOnVotableWithNullAcl()
    {
        $comment = $this->getVotableComment();
        $this->assertTrue($this->extension->canVote($comment));
    }

    public function testCannotVoteWhenCommentAclCannotView()
    {
        $comment = $this->getVotableComment();
        $commentAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\CommentAclInterface')->getMock();
        $commentAcl->expects($this->once())->method('canView')->with($comment)->will($this->returnValue(false));
        $voteAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\VoteAclInterface')->getMock();
        $voteAcl->expects($this->never())->method('canCreate');
        $extension = new CommentExtension($commentAcl, $voteAcl);
        $this->assertFalse($extension->canVote($comment));
    }

    public function testCanVoteWhenCommentAclCanView()
    {
        $comment = $this->getVotableComment();
        $commentAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\CommentAclInterface')->getMock();
        $commentAcl->expects($this->once())->method('canView')->with($comment)->will($this->returnValue(true));
        $voteAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\VoteAclInterface')->getMock();
        $voteAcl->expects($this->once())->method('canCreate')->will($this->returnValue(true));
        $extension = new CommentExtension($commentAcl, $voteAcl);
        $this->assertTrue($extension->canVote($comment));
    }

    public function testCanVoteWithNullCommentAcl()
    {
        $comment = $this->getVotableComment();
        $voteAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\VoteAclInterface')->getMock();
        $voteAcl->expects($this->once())->method('canCreate')->will($this->returnValue(true));
        $extension = new CommentExtension(null, $voteAcl);
        $this->assertTrue($extension->canVote($comment));
    }

    public function testIsDeletedWhenStateIsDeleted()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $comment->expects($this->once())->method('getState')->will($this->returnValue(\FOS\CommentBundle\Model\CommentInterface::STATE_DELETED));

        $extension = new CommentExtension();
        $this->assertTrue($extension->isCommentInState($comment, $comment::STATE_DELETED));
    }

    public function testIsDeletedWhenStateIsNotDeleted()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $comment->expects($this->once())->method('getState')->will($this->returnValue(\FOS\CommentBundle\Model\CommentInterface::STATE_VISIBLE));

        $extension = new CommentExtension();
        $this->assertFalse($extension->isCommentInState($comment, $comment::STATE_DELETED));
    }

    public function testCannotDeleteWhenNoCommentAcl()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $extension = new CommentExtension();
        $this->assertFalse($extension->canDeleteComment($comment));
    }

    public function testCanDeleteWhenCommentAclCanDelete()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $commentAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\CommentAclInterface')->getMock();
        $commentAcl->expects($this->once())->method('canDelete')->with($comment)->will($this->returnValue(true));
        $extension = new CommentExtension($commentAcl);
        $this->assertTrue($extension->canDeleteComment($comment));
    }

    public function testCannotDeleteWhenCommentAclCannotDelete()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $commentAcl = $this->getMockBuilder('FOS\CommentBundle\Acl\CommentAclInterface')->getMock();
        $commentAcl->expects($this->once())->method('canDelete')->with($comment)->will($this->returnValue(false));
        $extension = new CommentExtension($commentAcl);
        $this->assertFalse($extension->canDeleteComment($comment));
    }

    protected function getVotableComment()
    {
        return $this->getMockBuilder('FOS\CommentBundle\Model\VotableCommentInterface')->getMock();
    }
}
