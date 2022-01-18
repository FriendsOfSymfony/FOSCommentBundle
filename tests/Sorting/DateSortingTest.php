<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Sorting;

use FOS\CommentBundle\Sorting\DateSorting;
use PHPUnit\Framework\TestCase;

class DateSortingTest extends TestCase
{
    private $sorterAsc;
    private $sorterDesc;

    public function setUp(): void
    {
        $this->sorterAsc = new DateSorting('ASC');
        $this->sorterDesc = new DateSorting('DESC');
    }

    public function testEqual()
    {
        $comment1 = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $comment2 = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();

        $comment1->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(new \DateTime('2015-09-20 12:39:10')));

        $comment2->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(new \DateTime('2015-09-20 12:39:10')));

        $this->assertSame(0, $this->sorterAsc->doSort(['comment' => $comment1], ['comment' => $comment2]));
        $this->assertSame(0, $this->sorterDesc->doSort(['comment' => $comment1], ['comment' => $comment2]));
    }

    public function testGreaterOrLess()
    {
        $comment1 = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $comment2 = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();

        $comment1->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(new \DateTime('2015-08-20 12:39:10')));

        $comment2->expects($this->any())
            ->method('getCreatedAt')
            ->will($this->returnValue(new \DateTime('2015-09-20 12:39:10')));

        $this->assertSame(-1, $this->sorterAsc->doSort(['comment' => $comment1], ['comment' => $comment2]));
        $this->assertSame(1, $this->sorterAsc->doSort(['comment' => $comment2], ['comment' => $comment1]));
        $this->assertSame(-1, $this->sorterDesc->doSort(['comment' => $comment2], ['comment' => $comment1]));
        $this->assertSame(1, $this->sorterDesc->doSort(['comment' => $comment1], ['comment' => $comment2]));
    }
}
