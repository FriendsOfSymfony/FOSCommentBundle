<?php

namespace FOS\CommentBundle\Tests\EventListener;

use FOS\CommentBundle\Event\FlagEvent;
use FOS\CommentBundle\EventListener\FlagBlamerListener;
use FOS\CommentBundle\Model\FlagInterface;
use FOS\CommentBundle\Model\SignedFlagInterface;

class FlagBlamerListenerTest extends \PHPUnit_Framework_TestCase
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

    public function testNonSignedFlagIsNotBlamed()
    {
        $flag = $this->getMock(FlagInterface::class);
        $flag->expects($this->never())->method('setFlagger');
        $event = new FlagEvent($flag);
        $this->tokenStorage->expects($this->never())->method('getToken');
        $listener = new FlagBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testNullTokenIsNotBlamed()
    {
        $flag = $this->getSignedFlag();
        $flag->expects($this->never())->method('setFlagger');
        $event = new FlagEvent($flag);
        $this->tokenStorage->expects($this->once())->method('getToken')->will($this->returnValue(null));
        $listener = new FlagBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testAnonymousUserIsNotBlamed()
    {
        $flag = $this->getSignedFlag();
        $flag->expects($this->never())->method('setFlagger');
        $event = new FlagEvent($flag);
        $this->tokenStorage->expects($this->once())->method('getToken')->will($this->returnValue('some non-null'));
        $this->authorizationChecker->expects($this->once())->method('isGranted')->with('IS_AUTHENTICATED_REMEMBERED')->will($this->returnValue(false));
        $listener = new FlagBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testAuthenticatedUserIsBlamed()
    {
        $flag = $this->getSignedFlag();
        $flag->expects($this->once())->method('setFlagger');
        $event = new FlagEvent($flag);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())->method('getUser')->will($this->returnValue($this->getMock('Symfony\Component\Security\Core\User\UserInterface')));
        $this->authorizationChecker->expects($this->once())->method('isGranted')->with('IS_AUTHENTICATED_REMEMBERED')->will($this->returnValue(true));
        $this->tokenStorage->expects($this->exactly(2))->method('getToken')->will($this->returnValue($token));

        $listener = new FlagBlamerListener($this->authorizationChecker, $this->tokenStorage);
        $listener->blame($event);
    }

    public function testLoggerIsCalledForNonSignedFlag()
    {
        $flag = $this->getMock(FlagInterface::class);
        $event = new FlagEvent($flag);

        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger->expects($this->once())->method('debug')->with('Flag does not implement SignedFlagInterface, skipping');

        $listener = new FlagBlamerListener($this->authorizationChecker, $this->tokenStorage, $logger);
        $listener->blame($event);
    }

    public function testLoggerIsCalledForNullToken()
    {
        $flag = $this->getSignedFlag();
        $event = new FlagEvent($flag);

        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger->expects($this->once())->method('debug')->with('There is no firewall configured. We cant get a user.');

        $listener = new FlagBlamerListener($this->authorizationChecker, $this->tokenStorage, $logger);
        $listener->blame($event);
    }

    protected function getSignedFlag()
    {
        return $this->getMock(SignedFlagInterface::class);
    }
}
