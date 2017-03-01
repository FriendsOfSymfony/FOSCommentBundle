<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\CommentBundle\Tests\Event;

use FOS\CommentBundle\Event\VotePersistEvent;

class VotePersistEventTest extends \PHPUnit_Framework_TestCase
{
    public function testAbortingPersistence()
    {
        // @todo uncomment this in 3.0 and remove the abstract class.
        // $vote = $this->getMockBuilder('FOS\CommentBundle\Model\VoteInterface')->getMock();
        $vote = $this->getMockForAbstractClass('FOS\CommentBundle\Tests\Fixtures\AbstractVote');
        $event = new VotePersistEvent($vote);
        $this->assertFalse($event->isPersistenceAborted());
        $event->abortPersistence();
        $this->assertTrue($event->isPersistenceAborted());
    }
}
