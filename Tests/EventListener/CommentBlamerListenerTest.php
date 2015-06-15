<?php

namespace FOS\CommentBundle\Tests\EventListener;

use FOS\CommentBundle\EventListener\CommentBlamerListener;
use FOS\CommentBundle\Event\CommentEvent;

class CommentBlamerListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $authorizationChecker;
    protected $tokenStorage;

    public function setUp()
    {
        if (interface_exists('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')) {
            $this->tokenStorage = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface');
        } else {
            $this->tokenStorage = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        }

        if (interface_exists('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')) {
            $this->authorizationChecker = $this->getMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        } else {
            $this->authorizationChecker = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
        }
    }

    public function testNonSignedCommentIsNotBlamed()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $comment->expects($this->never())->method('setAuthor');
        $event = new CommentEvent($comment);
        $this->tokenStorage->expects($this->never())->method('getToken');
        $listener = new CommentBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testNullTokenIsNotBlamed()
    {
        $comment = $this->getSignedComment();
        $comment->expects($this->never())->method('setAuthor');
        $event = new CommentEvent($comment);
        $this->tokenStorage->expects($this->once())->method('getToken')->will($this->returnValue(null));
        $listener = new CommentBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testAnonymousUserIsNotBlamed()
    {
        $comment = $this->getSignedComment();
        $comment->expects($this->never())->method('setAuthor');
        $event = new CommentEvent($comment);
        $this->tokenStorage->expects($this->once())->method('getToken')->will($this->returnValue('some non-null'));
        $this->authorizationChecker->expects($this->once())->method('isGranted')->with('IS_AUTHENTICATED_REMEMBERED')->will($this->returnValue(false));
        $listener = new CommentBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testAuthenticatedUserIsBlamed()
    {
        $comment = $this->getSignedComment();
        $comment->expects($this->once())->method('setAuthor');
        $event = new CommentEvent($comment);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())->method('getUser')->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\User\UserInterface')));
        $this->authorizationChecker->expects($this->once())->method('isGranted')->with('IS_AUTHENTICATED_REMEMBERED')->will($this->returnValue(true));
        $this->tokenStorage->expects($this->exactly(2))->method('getToken')->will($this->returnValue($token));
        $listener = new CommentBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testEditingCommentDoesNotChangeBlame()
    {
        $comment = $this->getSignedComment();
        $comment->expects($this->never())->method('setAuthor');
        $comment->expects($this->once())->method('getAuthor')->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\User\UserInterface')));
        $event = new CommentEvent($comment);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->authorizationChecker->expects($this->never())->method('isGranted');
        $this->tokenStorage->expects($this->once())->method('getToken')->will($this->returnValue($token));

        $listener = new CommentBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testLoggerIsCalledForNonSignedComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $event = new CommentEvent($comment);

        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $logger->expects($this->once())->method('debug')->with('Comment does not implement SignedCommentInterface, skipping');

        $listener = new CommentBlamerListener($this->authorizationChecker, $this->tokenStorage, $logger);
        $listener->blame($event);
    }

    public function testLoggerIsCalledForNullToken()
    {
        $comment = $this->getSignedComment();
        $event = new CommentEvent($comment);

        $logger = $this->getMock('Symfony\Component\HttpKernel\Log\LoggerInterface');
        $logger->expects($this->once())->method('debug')->with('There is no firewall configured. We cant get a user.');

        $listener = new CommentBlamerListener($this->authorizationChecker, $this->tokenStorage, $logger);
        $listener->blame($event);
    }

    protected function getSignedComment()
    {
        return $this->getMock('FOS\CommentBundle\Model\SignedCommentInterface');
    }
}
