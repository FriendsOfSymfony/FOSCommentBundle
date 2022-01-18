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

use FOS\CommentBundle\Event\CommentPersistEvent;
use PHPUnit\Framework\TestCase;

class CommentPersistEventTest extends TestCase
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
