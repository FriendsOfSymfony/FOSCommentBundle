<?php

namespace FOS\CommentBundle\Tests\Sorting;

use FOS\CommentBundle\Sorting\DateSorting;

class DateSortingTest extends \PHPUnit_Framework_TestCase
{
    private $sorterAsc;
    private $sorterDesc;

    public function setUp()
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

        $this->assertEquals(0, $this->sorterAsc->doSort(array('comment' => $comment1), array('comment' => $comment2)));
        $this->assertEquals(0, $this->sorterDesc->doSort(array('comment' => $comment1), array('comment' => $comment2)));
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

        $this->assertEquals(-1, $this->sorterAsc->doSort(array('comment' => $comment1), array('comment' => $comment2)));
        $this->assertEquals(1, $this->sorterAsc->doSort(array('comment' => $comment2), array('comment' => $comment1)));
        $this->assertEquals(-1, $this->sorterDesc->doSort(array('comment' => $comment2), array('comment' => $comment1)));
        $this->assertEquals(1, $this->sorterDesc->doSort(array('comment' => $comment1), array('comment' => $comment2)));
    }
}
