<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Creator;

use FOS\CommentBundle\Creator\DefaultThreadCreator;

/**
 * Tests the functionality provided by Creator\DefaultThreadCreator.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class DefaultThreadCreatorTest extends \PHPUnit_Framework_TestCase
{
    protected $request;

    protected $thread;
    protected $threadManager;

    public function setUp()
    {
        $this->request = $this->getMock('Symfony\Component\HttpFoundation\Request');

        $this->thread = $this->getMock('FOS\CommentBundle\Model\ThreadInterface');
        $this->threadManager = $this->getMock('FOS\CommentBundle\Model\ThreadManagerInterface');
    }

    public function testCreate()
    {
        $id = 'hello';

        $this->threadManager->expects($this->once())
            ->method('createThread')
            ->will($this->returnValue($this->thread));

        $this->thread->expects($this->once())
            ->method('setId')
            ->with($id);

        $creator = new DefaultThreadCreator($this->request, $this->threadManager);
        $creator->create($id);
    }

}