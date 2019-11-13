<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality provided by Acl\AclCommentManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentTest extends TestCase
{
    public function testSetAncestorsAddsDepth()
    {
        $ancestors = [1, 5, 12, 14];
        $comment = new Comment();
        $comment->setAncestors($ancestors);

        $this->assertSame(count($ancestors), $comment->getDepth());
    }

    public function testSetParentSetsAncestors()
    {
        $ancestors = ['1', '5', '12'];
        $parentId = '14';

        $parent = $this->getMockBuilder('FOS\CommentBundle\Entity\Comment')->getMock();
        $parent->expects($this->once())
            ->method('getAncestors')
            ->will($this->returnValue($ancestors));
        $parent->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($parentId));

        $comment = new Comment();
        $comment->setParent($parent);

        $this->assertSame(array_merge($ancestors, [$parentId]), $comment->getAncestors());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetParentNotPersisted()
    {
        $parent = $this->getMockBuilder('FOS\CommentBundle\Entity\Comment')->getMock();
        $parent->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(null));

        $comment = new Comment();
        $comment->setParent($parent);
    }
}
