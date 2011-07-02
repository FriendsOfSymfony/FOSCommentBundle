<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\SpamDetection;

use FOS\CommentBundle\SpamDetection\NoopSpamDetection;

/**
 * Tests the functionality provided by Acl\AclCommentManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class NoopSpamDetectionTest extends \PHPUnit_Framework_TestCase
{
    public function testNoopSpamDetection()
    {
        $detection = new NoopSpamDetection();
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');

        $this->assertFalse($detection->isSpam($comment));
    }
}