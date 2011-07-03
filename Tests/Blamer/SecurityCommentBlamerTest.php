<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Blamer;

use FOS\CommentBundle\Blamer\SecurityCommentBlamer;

/**
 * Tests the functionality provided by Blamer\SecurityCommentBlamer.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SecurityCommentBlamerTest extends \PHPUnit_Framework_TestCase
{
    protected $securityContext;

    public function setUp()
    {
        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBlameAnonymousComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');

        $blamer = new SecurityCommentBlamer($this->securityContext);
        $blamer->blame($comment);
    }

    public function testBlameSignedCommentLoggedIn()
    {
        if (!interface_exists('FOS\UserBundle\Model\UserInterface')) {
            $this->markTestSkipped('Test requires FOSUserBundle to be present');
        }

        $user = $this->getMock('FOS\UserBundle\Model\UserInterface');

        $comment = $this->getMock('FOS\CommentBundle\Model\SignedCommentInterface');
        $comment->expects($this->once())
            ->method('setAuthor')
            ->with($user);

        $token = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $token->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));

        $this->securityContext->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($token));

        $this->securityContext->expects($this->once())
            ->method('isGranted')
            ->with('IS_AUTHENTICATED_REMEMBERED')
            ->will($this->returnValue(true));

        $blamer = new SecurityCommentBlamer($this->securityContext);
        $blamer->blame($comment);
    }

    public function testBlameSignedCommentLoggedOut()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\SignedCommentInterface');
        $comment->expects($this->never())
            ->method('setAuthor');

        $this->securityContext->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue(true));

        $this->securityContext->expects($this->once())
            ->method('isGranted')
            ->with('IS_AUTHENTICATED_REMEMBERED')
            ->will($this->returnValue(false));

        $blamer = new SecurityCommentBlamer($this->securityContext);
        $blamer->blame($comment);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoFirewallBlameFailure()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\SignedCommentInterface');
        $comment->expects($this->never())
            ->method('setAuthor');

        $this->securityContext->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue(null));

        $blamer = new SecurityCommentBlamer($this->securityContext);
        $blamer->blame($comment);
    }
}