<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Entity;

use FOS\CommentBundle\Entity\Comment as BaseComment;

/**
 * Tests the functionality provided by Model\Vote.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class VoteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetZeroVoteValue()
    {
        $vote = new Vote();
        $vote->setValue(0);
    }
}