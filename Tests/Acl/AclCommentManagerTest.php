<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Acl;

use FOS\CommentBundle\Acl\AclCommentManager;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\CommentManagerInterface;
use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Tests the functionality provided by Acl\AclCommentManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class AclCommentManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $realManager;
    protected $commentSecurity;
    protected $threadSecurity;
    protected $thread;
    protected $comment;
    protected $sorting_strategy;
    protected $depth;
    protected $result;
    protected $commentId;

    public function setUp()
    {
        $this->realManager = $this->getMock('FOS\CommentBundle\Model\CommentManagerInterface');
        $this->commentSecurity = $this->getMock('FOS\CommentBundle\Acl\CommentAclInterface');
        $this->threadSecurity = $this->getMock('FOS\CommentBundle\Acl\ThreadAclInterface');
        $this->thread = $this->getMock('FOS\CommentBundle\Model\ThreadInterface');
        $this->comment = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $this->sorting_strategy = 'date_asc';
        $this->depth = 0;
        $this->result = array();
        $this->commentId = null;
    }

    protected function configureCommentSecurity($method, $return)
    {
        $this->commentSecurity->expects($this->any())
             ->method($method)
             ->will($this->returnValue($return));
    }

    public function treeSetup()
    {
        $this->result = array(array('comment' => $this->comment, 'children' => array()));

        $this->realManager->expects($this->once())
             ->method('findCommentTreeByThread')
             ->with($this->equalTo($this->thread),
                   $this->equalTo($this->sorting_strategy),
                   $this->equalTo($this->depth))
             ->will($this->returnValue($this->result));
    }

    /**
     * @covers AclCommentManager::findCommentTreeByThread
     */
    public function testFindCommentTreeByThreadReturnsResults()
    {
        $this->treeSetup();
        $this->configureCommentSecurity('canView', true);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $this->assertEquals($this->result, $manager->findCommentTreeByThread($this->thread, $this->sorting_strategy, $this->depth));
    }

    /**
     * @covers AclCommentManager::findCommentTreeByThread
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testAccessDeniedFindCommentTreeByThread()
    {
        $this->treeSetup();
        $this->configureCommentSecurity('canView', false);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $manager->findCommentTreeByThread($this->thread, $this->sorting_strategy, $this->depth);
    }

    protected function findByIdSetup()
    {
        $this->commentId = 123;
        $this->result = $this->comment;

        $this->realManager->expects($this->once())
            ->method('findCommentById')
            ->with($this->commentId)
            ->will($this->returnValue($this->result));
    }

    /**
     * @covers AclCommentManager::findCommentById
     */
    public function testFindCommentById()
    {
        $this->findByIdSetup();
        $this->configureCommentSecurity('canView', true);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $this->assertEquals($this->result, $manager->findCommentById($this->commentId));
    }

    /**
     * @covers AclCommentManager::findCommentById
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testAccessDeniedFindCommentById()
    {
        $this->findByIdSetup();
        $this->configureCommentSecurity('canView', false);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $manager->findCommentById($this->commentId);
    }
}