<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\EventListener;

use FOS\CommentBundle\Model\VoteManagerInterface;
use FOS\CommentBundle\Event\VoteEvent;
use FOS\CommentBundle\Event\VotePersistEvent;
use FOS\CommentBundle\EventListener\CommentVoteScoreListener;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Tests\Entity\CommentVotable;
use FOS\CommentBundle\Tests\Entity\Vote;
use PHPUnit\Framework\TestCase;

class CommentVoteScoreListenerTest extends TestCase
{
    private $voteManager;

    public function setUp()
    {
        $this->voteManager = $this->getMockBuilder(VoteManagerInterface::class)->getMock();
    }

    public function testIncrementScore()
    {
        $comment = new CommentVotable();
        $comment->setScore(20);

        $vote = new Vote();
        $vote->setValue(VoteInterface::VOTE_UP);
        $vote->setComment($comment);

        $this->voteManager->expects($this->once())
            ->method('isNewVote')
            ->will($this->returnValue(true));

        $listener = new CommentVoteScoreListener($this->voteManager, false);
        $listener->onVotePersist(new VotePersistEvent($vote));

        $this->assertSame(21, $comment->getScore());
    }

    public function testDecrementScore()
    {
        $comment = new CommentVotable();
        $comment->setScore(19);

        $vote = new Vote();
        $vote->setValue(VoteInterface::VOTE_DOWN);
        $vote->setComment($comment);

        $listener = new CommentVoteScoreListener($this->voteManager, false);
        $listener->onVoteRemove(new VoteEvent($vote));

        $this->assertSame(20, $comment->getScore());
    }
}