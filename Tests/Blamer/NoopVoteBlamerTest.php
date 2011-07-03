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

use FOS\CommentBundle\Blamer\NoopVoteBlamer;

/**
 * Tests the functionality provided by Blamer\NoopVoteBlamer.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class NoopVoteBlamerTest extends \PHPUnit_Framework_TestCase
{
    public function testNoopBlamerVote()
    {
        $vote = $this->getMock('FOS\CommentBundle\Model\VoteInterface');

        $vote->expects($this->never())
            ->method('setVoter');

        $blamer = new NoopVoteBlamer();
        $blamer->blame($vote);
    }
}