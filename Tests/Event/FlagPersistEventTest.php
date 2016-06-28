<?php

namespace FOS\CommentBundle\Tests\Event;

use FOS\CommentBundle\Event\FlagPersistEvent;
use FOS\CommentBundle\Model\FlagInterface;

class FlagPersistEventTest extends \PHPUnit_Framework_TestCase
{
    public function testAbortingPersistence()
    {
        $flag = $this->getMock(FlagInterface::class);
        $event = new FlagPersistEvent($flag);
        $this->assertFalse($event->isPersistenceAborted());
        $event->abortPersistence();
        $this->assertTrue($event->isPersistenceAborted());
    }
}
