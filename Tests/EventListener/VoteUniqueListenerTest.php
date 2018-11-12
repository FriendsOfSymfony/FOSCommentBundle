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

use FOS\CommentBundle\Event\VotePersistEvent;
use FOS\CommentBundle\EventListener\VoteUniqueListener;
use FOS\CommentBundle\Model\SignedVoteInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;
use FOS\CommentBundle\Tests\Entity\Comment;
use FOS\CommentBundle\Tests\Fixtures\AbstractVote;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class VoteUniqueListenerTest extends TestCase
{
    private $tokenStorage;
    private $voteManager;
    private $logger;
    private $vote;

    public function setUp()
    {
        $this->tokenStorage = $this->getMockBuilder(TokenStorageInterface::class)->getMock();
        $this->voteManager = $this->getMockBuilder(VoteManagerInterface::class)->getMock();
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->vote = $this->getMockBuilder(SignedVoteInterface::class)->getMock();
    }

    public function testLoggerIsCalledForExistsVote()
    {
        $user = $this->getMockBuilder(UserInterface::class)->getMock();
        $user->expects($this->once())
            ->method('getUsername')
            ->will($this->returnValue('username'));

        $comment = $this->getMockBuilder(Comment::class)->getMock();
        $comment->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(11));

        $this->vote->expects($this->exactly(2))
            ->method('getVoter')
            ->will($this->returnValue($user));

        $this->vote->expects($this->once())
            ->method('getComment')
            ->will($this->returnValue($comment));

        $this->voteManager->expects($this->once())
            ->method('findVoteBy')
            ->will($this->returnValue($this->getMockBuilder(SignedVoteInterface::class)->getMock()));

        $event = new VotePersistEvent($this->vote);

        $this->logger->expects($this->once())
            ->method('debug')
            ->with(sprintf('User "%s" already added a vote for comment with ID "%s"', 'username', '11'));

        $listener = new VoteUniqueListener($this->voteManager, $this->tokenStorage, $this->logger);
        $listener->validatePersistence($event);

        $this->assertTrue($event->isPersistenceAborted());
    }

    public function testLoggerIsCalledForNullToken()
    {
        $token = $this->getMockBuilder(TokenInterface::class)->getMock();

        $token->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue(null));

        $this->tokenStorage->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));

        $this->vote->expects($this->once())
            ->method('getVoter')
            ->will($this->returnValue(null));

        $event = new VotePersistEvent($this->vote);

        $this->logger->expects($this->once())
            ->method('debug')
            ->with('There is no firewall configured. We cant get a user.');

        $listener = new VoteUniqueListener($this->voteManager, $this->tokenStorage, $this->logger);
        $listener->validatePersistence($event);

        $this->assertTrue($event->isPersistenceAborted());
    }

    public function testLoggerIsCalledForNonSignedVote()
    {
        $vote = $this->getMockForAbstractClass(AbstractVote::class);
        $event = new VotePersistEvent($vote);
        
        $this->logger->expects($this->once())
            ->method('debug')
            ->with(sprintf('Vote does not implement "%s", skipping', SignedVoteInterface::class));

        $listener = new VoteUniqueListener($this->voteManager, $this->tokenStorage, $this->logger);
        $listener->validatePersistence($event);

        $this->assertTrue($event->isPersistenceAborted());
    }
}