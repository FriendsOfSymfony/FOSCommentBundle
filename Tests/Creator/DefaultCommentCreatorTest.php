<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Creator;

use FOS\CommentBundle\Blamer\CommentBlamerInterface;
use FOS\CommentBundle\Creator\DefaultCommentCreator;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\CommentManagerInterface;
use FOS\CommentBundle\SpamDetection\SpamDetectionInterface;

/**
 * Tests the functionality provided by Acl\AclCommentManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class DefaultCommentCreatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $manager = $this->getMock('FOS\CommentBundle\Model\CommentManagerInterface');
        $manager->expects($this->once())
            ->method('addComment')
            ->with($comment)
            ->will($this->returnValue($comment));

        $blamer = $this->getMock('FOS\CommentBundle\Blamer\CommentBlamerInterface');
        $blamer->expects($this->once())
            ->method('blame')
            ->with($comment);

        $spam = $this->getMock('FOS\CommentBundle\SpamDetection\SpamDetectionInterface');
        $spam->expects($this->once())
            ->method('isSpam')
            ->with($comment)
            ->will($this->returnValue(false));

        $creator = new \FOS\CommentBundle\Creator\DefaultCommentCreator($manager, $blamer, $spam);
        $this->assertTrue($creator->create($comment));
    }

    public function testCreateCommentIsSpam()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $manager = $this->getMock('FOS\CommentBundle\Model\CommentManagerInterface');
        $manager->expects($this->never())
            ->method('addComment');

        $blamer = $this->getMock('FOS\CommentBundle\Blamer\CommentBlamerInterface');
        $blamer->expects($this->once())
            ->method('blame')
            ->with($comment);

        $spam = $this->getMock('FOS\CommentBundle\SpamDetection\SpamDetectionInterface');
        $spam->expects($this->once())
            ->method('isSpam')
            ->with($comment)
            ->will($this->returnValue(true));

        $creator = new \FOS\CommentBundle\Creator\DefaultCommentCreator($manager, $blamer, $spam);
        $this->assertFalse($creator->create($comment));
    }
}