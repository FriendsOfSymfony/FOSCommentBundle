<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Event;

use FOS\CommentBundle\Event\VotePersistEvent;
use PHPUnit\Framework\TestCase;

class VotePersistEventTest extends TestCase
{
    public function testAbortingPersistence()
    {
        $vote = $this->getMockBuilder('FOS\CommentBundle\Model\VoteInterface')->getMock();
        $event = new VotePersistEvent($vote);
        $this->assertFalse($event->isPersistenceAborted());
        $event->abortPersistence();
        $this->assertTrue($event->isPersistenceAborted());
    }
}
