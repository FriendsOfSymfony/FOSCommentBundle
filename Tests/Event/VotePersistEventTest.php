<?php

namespace FOS\CommentBundle\Tests\Event;

use FOS\CommentBundle\Event\VotePersistEvent;

class VotePersistEventTest extends \PHPUnit_Framework_TestCase
{
    public function testAbortingPersistence()
    {
        $vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');
        $event = new VotePersistEvent($vote);
        $this->assertFalse($event->isPersistenceAborted());
        $event->abortPersistence();
        $this->assertTrue($event->isPersistenceAborted());
    }
}
