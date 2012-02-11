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

use FOS\CommentBundle\Twig\CommentExtension;

/**
 * Tests the functionality provided by Twig\Extension.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentExtensionTest extends \PHPUnit_Framework_TestCase
{
    protected $extension;

    public function setUp()
    {
        $this->extension = new CommentExtension();
    }

    public function testIsVotableNonObject()
    {
        $this->assertFalse($this->extension->isVotable('NotAVoteObject'));
    }

    public function testIsntVotable()
    {
        $this->assertFalse($this->extension->isVotable(new \StdClass()));
    }

    public function testIsVotable()
    {
        $votableComment = $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');
        $this->assertTrue($this->extension->isVotable($votableComment));
    }

    public function testCanCreateRootCommentWithNullAcl()
    {
        $this->assertTrue($this->extension->canComment());
    }

    public function testCanCreateRootCommentWithAcl()
    {
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canCreate')->will($this->returnValue(true));
        $extension = new CommentExtension($commentAcl);
        $this->assertTrue($extension->canComment());
    }

    public function testCannotCreateRootCommentWithAcl()
    {
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canCreate')->will($this->returnValue(false));
        $extension = new CommentExtension($commentAcl);
        $this->assertFalse($extension->canComment());
    }

    public function testAclCanReplyToComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canReply')->with($comment)->will($this->returnValue(true));
        $extension = new CommentExtension($commentAcl);
        $this->assertTrue($extension->canComment($comment));
    }

    public function testAclCannotReplyToComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canReply')->with($comment)->will($this->returnValue(false));
        $extension = new CommentExtension($commentAcl);
        $this->assertFalse($extension->canComment($comment));
    }

    public function testCannotVoteOnNonVotable()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
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
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canView')->with($comment)->will($this->returnValue(false));
        $voteAcl = $this->getMock('FOS\CommentBundle\Acl\VoteAclInterface');
        $voteAcl->expects($this->never())->method('canCreate');
        $extension = new CommentExtension($commentAcl, $voteAcl);
        $this->assertFalse($extension->canVote($comment));
    }

    public function testCanVoteWhenCommentAclCanView()
    {
        $comment = $this->getVotableComment();
        $commentAcl = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $commentAcl->expects($this->once())->method('canView')->with($comment)->will($this->returnValue(true));
        $voteAcl = $this->getMock('FOS\CommentBundle\Acl\VoteAclInterface');
        $voteAcl->expects($this->once())->method('canCreate')->will($this->returnValue(true));
        $extension = new CommentExtension($commentAcl, $voteAcl);
        $this->assertTrue($extension->canVote($comment));
    }

    public function testCanVoteWithNullCommentAcl()
    {
        $comment = $this->getVotableComment();
        $voteAcl = $this->getMock('FOS\CommentBundle\Acl\VoteAclInterface');
        $voteAcl->expects($this->once())->method('canCreate')->will($this->returnValue(true));
        $extension = new CommentExtension(null, $voteAcl);
        $this->assertTrue($extension->canVote($comment));
    }

    protected function getVotableComment()
    {
        return $this->getMock('FOS\CommentBundle\Model\VotableCommentInterface');
    }
}