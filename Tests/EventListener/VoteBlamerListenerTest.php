<?php

namespace FOS\CommentBundle\Tests\EventListener;

use FOS\CommentBundle\EventListener\VoteBlamerListener;
use FOS\CommentBundle\Event\VoteEvent;

class VoteBlamerListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $authorizationChecker;
    protected $tokenStorage;

    public function setUp()
    {
        if (interface_exists('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')) {
            $this->tokenStorage = $this->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')->getMock();
        } else {
            $this->tokenStorage = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')->getMock();
        }

        if (interface_exists('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')) {
            $this->authorizationChecker = $this->getMockBuilder('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')->getMock();
        } else {
            $this->authorizationChecker = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')->getMock();
        }
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
        // @todo uncomment this in 3.0 and remove the abstract class.
        // $vote = $this->getMockBuilder('FOS\CommentBundle\Model\VoteInterface')->getMock();
        $vote = $this->getMockForAbstractClass('FOS\CommentBundle\Tests\Fixtures\AbstractVote');
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
