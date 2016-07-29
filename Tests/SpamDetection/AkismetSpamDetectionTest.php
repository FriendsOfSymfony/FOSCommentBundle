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

use FOS\CommentBundle\SpamDetection\AkismetSpamDetection;

/**
 * Tests the functionality provided by Acl\AclCommentManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class AkismetSpamDetectionTest extends \PHPUnit_Framework_TestCase
{
    protected $akismet;
    protected $detector;

    public function setUp()
    {
        if (!interface_exists('Ornicar\AkismetBundle\Akismet\AkismetInterface')) {
            $this->markTestSkipped('Ornicar\AkismetBundle is not installed');
        }

        $this->akismet = $this->getMockBuilder('Ornicar\AkismetBundle\Akismet\AkismetInterface')->getMock();
        $this->detector = new AkismetSpamDetection($this->akismet);
    }

    public function testAkismetSpamDetection()
    {
        $comment = $this->getMockBuilder('FOS\CommentBundle\Model\CommentInterface')->getMock();

        $this->akismet->expects($this->once())
            ->method('isSpam')
            ->will($this->returnValue(false));

        $this->assertFalse($this->detector->isSpam($comment));
    }
}
