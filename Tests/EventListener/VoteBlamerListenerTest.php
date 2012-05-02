<?php

namespace FOS\CommentBundle\Tests\EventListener;

use FOS\CommentBundle\EventListener\VoteBlamerListener;
use FOS\CommentBundle\Event\VoteEvent;

class VoteBlamerListenerTest extends \PHPUnit_Framework_TestCase
{
    public function testNonSignedVoteIsNotBlamed()
    {
        $vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');
        $vote->expects($this->never())->method('setVoter');
        $event = new VoteEvent($vote);
        $securityContext = $this->getSecurityContext();
        $securityContext->expects($this->never())->method('getToken');
        $listener = new VoteBlamerListener($securityContext);
        $listener->blame($event);
    }

    public function testNullTokenIsNotBlamed()
    {
        $vote = $this->getSignedVote();
        $vote->expects($this->never())->method('setVoter');
        $event = new VoteEvent($vote);
        $securityContext = $this->getSecurityContext();
        $securityContext->expects($this->once())->method('getToken')->will($this->returnValue(null));
        $listener = new VoteBlamerListener($securityContext);
        $listener->blame($event);
    }

    public function testAnonymousUserIsNotBlamed()
    {
        $vote = $this->getSignedVote();
        $vote->expects($this->never())->method('setVoter');
        $event = new VoteEvent($vote);
        $securityContext = $this->getSecurityContext();
        $securityContext->expects($this->once())->method('getToken')->will($this->returnValue('some non-null'));
        $securityContext->expects($this->once())->method('isGranted')->with('IS_AUTHENTICATED_REMEMBERED')->will($this->returnValue(false));
        $listener = new VoteBlamerListener($securityContext);
        $listener->blame($event);
    }

    public function testAuthenticatedUserIsBlamed()
    {
        $vote = $this->getSignedVote();
        $vote->expects($this->once())->method('setVoter');
        $event = new VoteEvent($vote);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())->method('getUser')->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\User\UserInterface')));
        $securityContext = $this->getSecurityContext();
        $securityContext->expects($this->once())->method('isGranted')->with('IS_AUTHENTICATED_REMEMBERED')->will($this->returnValue(true));
        $securityContext->expects($this->exactly(2))->method('getToken')->will($this->returnValue($token));

        $listener = new VoteBlamerListener($securityContext);
        $listener->blame($event);
    }

    public function testLoggerIsCalledForNonSignedVote()
    {
        $vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');
        $event = new VoteEvent($vote);

        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $logger->expects($this->once())->method('debug')->with('Vote does not implement SignedVoteInterface, skipping');

        $listener = new VoteBlamerListener($this->getSecurityContext(), $logger);
        $listener->blame($event);
    }

    public function testLoggerIsCalledForNullToken()
    {
        $vote = $this->getSignedVote();
        $event = new VoteEvent($vote);

        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $logger->expects($this->once())->method('debug')->with('There is no firewall configured. We cant get a user.');

        $listener = new VoteBlamerListener($this->getSecurityContext(), $logger);
        $listener->blame($event);
    }

    protected function getSignedVote()
    {
        return $this->getMock('FOS\CommentBundle\Model\SignedVoteInterface');
    }

    protected function getSecurityContext()
    {
        return $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
    }
}
