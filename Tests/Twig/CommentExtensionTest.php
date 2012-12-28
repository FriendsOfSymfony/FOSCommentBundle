<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Twig;

use FOS\CommentBundle\Sorting\SortingFactory;
use FOS\CommentBundle\Twig\CommentExtension;

/**
 * Tests the functionality provided by Twig\Extension.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;

    protected function getCommentExtension($commentAcl = null, $voteAcl = null)
    {
        return new CommentExtension(new SortingFactory(array(), ''), $commentAcl, $voteAcl);
    }

    public function testIsVotableNonObject()
    {
        $this->assertFalse($this->getCommentExtension()->isVotable('NotAVoteObject'));
    }

    public function testIsntVotable()
    {
        $this->assertFalse($this->getCommentExtension()->isVotable(new \StdClass()));
    }

    public function testIsVotable()
    {
        $votableComment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');

        $this->assertTrue($this->getCommentExtension()->isVotable($votableComment));
    }

    public function testCanCreateRootCommentWithNullAcl()
    {
        $this->assertTrue($this->getCommentExtension()->canComment());
    }

    public function testCanCreateRootCommentWithAcl()
    {
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canCreate')->will($this->returnValue(true));
        $extension = $this->getCommentExtension($commentAcl);
        $this->assertTrue($extension->canComment());
    }

    public function testCannotCreateCommentOnClosedThread()
    {
        $thread = $this->getMock('FOS\CommentBundle\Model\ThreadInterface');
        $thread->expects($this->once())->method('isCommentable')->will($this->returnValue(false));
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $comment->expects($this->exactly(2))->method('getThread')->will($this->returnValue($thread));
        $extension = $this->getCommentExtension();
        $this->assertFalse($extension->canComment($comment));
    }

    public function testCannotCreateRootCommentWithAcl()
    {
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canCreate')->will($this->returnValue(false));
        $extension = $this->getCommentExtension($commentAcl);
        $this->assertFalse($extension->canComment());
    }

    public function testAclCanReplyToComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canReply')->with($comment)->will($this->returnValue(true));
        $extension = $this->getCommentExtension($commentAcl);
        $this->assertTrue($extension->canComment($comment));
    }

    public function testAclCannotReplyToComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canReply')->with($comment)->will($this->returnValue(false));
        $extension = $this->getCommentExtension($commentAcl);
        $this->assertFalse($extension->canComment($comment));
    }

    public function testCannotVoteOnNonVotable()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $this->assertFalse($this->getCommentExtension()->canVote($comment));
    }

    public function testCanVoteOnVotableWithNullAcl()
    {
        $comment = $this->getVotableComment();
        $this->assertTrue($this->getCommentExtension()->canVote($comment));
    }

    public function testCannotVoteWhenCommentAclCannotView()
    {
        $comment = $this->getVotableComment();
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canView')->with($comment)->will($this->returnValue(false));
        $voteAcl = $this->getMock('FOS\CommentBundle\Acl\VoteAclInterface');
        $voteAcl->expects($this->never())->method('canCreate');
        $extension = $this->getCommentExtension($commentAcl, $voteAcl);
        $this->assertFalse($extension->canVote($comment));
    }

    public function testCanVoteWhenCommentAclCanView()
    {
        $comment = $this->getVotableComment();
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canView')->with($comment)->will($this->returnValue(true));
        $voteAcl = $this->getMock('FOS\CommentBundle\Acl\VoteAclInterface');
        $voteAcl->expects($this->once())->method('canCreate')->will($this->returnValue(true));
        $extension = $this->getCommentExtension($commentAcl, $voteAcl);
        $this->assertTrue($extension->canVote($comment));
    }

    public function testCanVoteWithNullCommentAcl()
    {
        $comment = $this->getVotableComment();
        $voteAcl = $this->getMock('FOS\CommentBundle\Acl\VoteAclInterface');
        $voteAcl->expects($this->once())->method('canCreate')->will($this->returnValue(true));
        $extension = $this->getCommentExtension(null, $voteAcl);
        $this->assertTrue($extension->canVote($comment));
    }

    public function testIsDeletedWhenStateIsDeleted()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $comment->expects($this->once())->method('getState')->will($this->returnValue(\FOS\CommentBundle\Model\CommentInterface::STATE_DELETED));

        $extension = $this->getCommentExtension();
        $this->assertTrue($extension->isCommentInState($comment, $comment::STATE_DELETED));
    }

    public function testIsDeletedWhenStateIsNotDeleted()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $comment->expects($this->once())->method('getState')->will($this->returnValue(\FOS\CommentBundle\Model\CommentInterface::STATE_VISIBLE));

        $extension = $this->getCommentExtension();
        $this->assertFalse($extension->isCommentInState($comment, $comment::STATE_DELETED));
    }

    public function testCanDeleteWhenNoCommentAcl()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $extension = $this->getCommentExtension();
        $this->assertTrue($extension->canDeleteComment($comment));
    }

    public function testCanDeleteWhenCommentAclCanDelete()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canDelete')->with($comment)->will($this->returnValue(true));
        $extension = $this->getCommentExtension($commentAcl);
        $this->assertTrue($extension->canDeleteComment($comment));
    }

    public function testCannotDeleteWhenCommentAclCannotDelete()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canDelete')->with($comment)->will($this->returnValue(false));
        $extension = $this->getCommentExtension($commentAcl);
        $this->assertFalse($extension->canDeleteComment($comment));
    }

    protected function getVotableComment()
    {
        return $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');
    }
}
