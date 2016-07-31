<?php

namespace FOS\CommentBundle\Tests\Event;

use FOS\CommentBundle\Event\CommentPersistEvent;

class CommentPersistEventTest extends \PHPUnit_Framework_TestCase
{
    public function testAbortingPersistence()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();
        $event = new CommentPersistEvent($comment);
        $this->assertFalse($event->isPersistenceAborted());
        $event->abortPersistence();
        $this->assertTrue($event->isPersistenceAborted());
    }
}
