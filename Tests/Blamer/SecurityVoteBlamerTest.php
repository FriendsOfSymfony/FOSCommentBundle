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

use FOS\CommentBundle\Blamer\SecurityVoteBlamer;

/**
 * Tests the functionality provided by Blamer\SecurityVoteBlamer.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SecurityVoteBlamerTest extends \PHPUnit_Framework_TestCase
{
    protected $securityContext;

    public function setUp()
    {
        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testBlameAnonymousVote()
    {
        $vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');

        $blamer = new SecurityVoteBlamer($this->securityContext);
        $blamer->blame($vote);
    }

    public function testBlameSignedCommentLoggedIn()
    {
        if (!interface_exists('FOS\UserBundle\Model\UserInterface')) {
            $this->markTestSkipped('Test requires FOSUserBundle to be present');
        }

        $user = $this->getMock('FOS\UserBundle\Model\UserInterface');

        $vote = $this->getMock('FOS\CommentBundle\Model\SignedVoteInterface');
        $vote->expects($this->once())
            ->method('setVoter')
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

        $blamer = new SecurityVoteBlamer($this->securityContext);
        $blamer->blame($vote);
    }

    public function testBlameSignedCommentLoggedOut()
    {
        $vote = $this->getMock('FOS\CommentBundle\Model\SignedVoteInterface');
        $vote->expects($this->never())
            ->method('setVoter');

        $this->securityContext->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue(true));

        $this->securityContext->expects($this->once())
            ->method('isGranted')
            ->with('IS_AUTHENTICATED_REMEMBERED')
            ->will($this->returnValue(false));

        $blamer = new SecurityVoteBlamer($this->securityContext);
        $blamer->blame($vote);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testNoFirewallBlameFailure()
    {
        $vote = $this->getMock('FOS\CommentBundle\Model\SignedVoteInterface');
        $vote->expects($this->never())
            ->method('setVoter');

        $this->securityContext->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue(null));

        $blamer = new SecurityVoteBlamer($this->securityContext);
        $blamer->blame($vote);
    }
}