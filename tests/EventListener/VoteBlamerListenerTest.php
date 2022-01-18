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

use FOS\CommentBundle\Event\VoteEvent;
use FOS\CommentBundle\EventListener\VoteBlamerListener;
use PHPUnit\Framework\TestCase;

class VoteBlamerListenerTest extends TestCase
{
    protected $authorizationChecker;
    protected $tokenStorage;

    public function setUp(): void
    {
        $this->tokenStorage = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')->getMock();
        $this->authorizationChecker = $this->getMockBuilder('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')->getMock();
    }

    public function testNonSignedVoteIsNotBlamed()
    {
        $vote = $this->getMockBuilder('FOS\CommentBundle\Model\VoteInterface')->getMock();
        $event = new VoteEvent($vote);
        $this->tokenStorage->expects($this->never())->method('getToken');
        $listener = new VoteBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testNullTokenIsNotBlamed()
    {
        $vote = $this->getSignedVote();
        $vote->expects($this->never())->method('setVoter');
        $event = new VoteEvent($vote);
        $this->tokenStorage->expects($this->once())->method('getToken')->will($this->returnValue(null));
        $listener = new VoteBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testAnonymousUserIsNotBlamed()
    {
        $vote = $this->getSignedVote();
        $vote->expects($this->never())->method('setVoter');
        $event = new VoteEvent($vote);
        $this->tokenStorage->expects($this->once())->method('getToken')->will($this->returnValue('some non-null'));
        $this->authorizationChecker->expects($this->once())->method('isGranted')->with('IS_AUTHENTICATED_REMEMBERED')->will($this->returnValue(false));
        $listener = new VoteBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testAuthenticatedUserIsBlamed()
    {
        $vote = $this->getSignedVote();
        $vote->expects($this->once())->method('setVoter');
        $event = new VoteEvent($vote);

        $token = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')->getMock();
        $token->expects($this->once())->method('getUser')->will($this->returnValue($this->getMockBuilder('Symfony\Component\Security\Core\User\UserInterface')->getMock()));
        $this->authorizationChecker->expects($this->once())->method('isGranted')->with('IS_AUTHENTICATED_REMEMBERED')->will($this->returnValue(true));
        $this->tokenStorage->expects($this->exactly(2))->method('getToken')->will($this->returnValue($token));

        $listener = new VoteBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testLoggerIsCalledForNonSignedVote()
    {
        $vote = $this->getMockBuilder('FOS\CommentBundle\Model\VoteInterface')->getMock();
        $event = new VoteEvent($vote);

        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();
        $logger->expects($this->once())->method('debug')->with('Vote does not implement SignedVoteInterface, skipping');

        $listener = new VoteBlamerListener($this->authorizationChecker, $this->tokenStorage, $logger);
        $listener->blame($event);
    }

    public function testLoggerIsCalledForNullToken()
    {
        $vote = $this->getSignedVote();
        $event = new VoteEvent($vote);

        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();
        $logger->expects($this->once())->method('debug')->with('There is no firewall configured. We cant get a user.');

        $listener = new VoteBlamerListener($this->authorizationChecker, $this->tokenStorage, $logger);
        $listener->blame($event);
    }

    protected function getSignedVote()
    {
        return $this->getMockBuilder('FOS\CommentBundle\Model\SignedVoteInterface')->getMock();
    }
}
