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

use FOS\CommentBundle\Blamer\NoopCommentBlamer;

/**
 * Tests the functionality provided by Blamer\NoopCommentBlamer.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class NoopCommentBlamerTest extends \PHPUnit_Framework_TestCase
{
    public function testNoopBlamerComment()
    {
        $comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');

        $comment->expects($this->never())
            ->method('setAuthor');

        $blamer = new NoopCommentBlamer();
        $blamer->blame($comment);
    }
}