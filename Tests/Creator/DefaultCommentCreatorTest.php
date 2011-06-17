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
    protected $comment;
    protected $manager;
    protected $blamer;
    protected $spam;

    public function setUp()
    {
        $this->comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $this->manager = $this->getMock('FOS\CommentBundle\Model\CommentManagerInterface');
        $this->blamer = $this->getMock('FOS\CommentBundle\Blamer\CommentBlamerInterface');
        $this->spam = $this->getMock('FOS\CommentBundle\SpamDetection\SpamDetectionInterface');
    }

    /**
     * @ covers DefaultCommentCreator::create
     */
    public function testCreate()
    {
        $this->manager->expects($this->once())
            ->method('addComment')
            ->with($this->comment);

        $this->blamer->expects($this->once())
            ->method('blame')
            ->with($this->comment);

        $this->spam->expects($this->once())
            ->method('isSpam')
            ->with($this->comment)
            ->will($this->returnValue(false));

        $creator = new \FOS\CommentBundle\Creator\DefaultCommentCreator($this->manager, $this->blamer, $this->spam);
        $this->assertTrue($creator->create($this->comment));
    }

    /**
     * @ covers DefaultCommentCreator::create
     */
    public function testCreateCommentIsSpam()
    {
        $this->manager->expects($this->never())
            ->method('addComment');

        $this->spam->expects($this->once())
            ->method('isSpam')
            ->with($this->comment)
            ->will($this->returnValue(true));

        $creator = new \FOS\CommentBundle\Creator\DefaultCommentCreator($this->manager, $this->blamer, $this->spam);
        $this->assertFalse($creator->create($this->comment));
    }
}