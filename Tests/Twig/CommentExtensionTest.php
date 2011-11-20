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
}