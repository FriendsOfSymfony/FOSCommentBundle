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
    protected $parent;

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
        $this->parent = null;
    }

    protected function commentReturnsThread()
    {
        $this->comment->expects($this->once())
            ->method('getThread')
            ->will($this->returnValue($this->thread));
    }

    protected function configureCommentSecurity($method, $return)
    {
        $this->commentSecurity->expects($this->any())
             ->method($method)
             ->will($this->returnValue($return));
    }

    protected function configureThreadSecurity($method, $return)
    {
        $this->threadSecurity->expects($this->any())
             ->method($method)
             ->will($this->returnValue($return));
    }

    /**
     * @covers AclCommentManager::findCommentTreeByThread
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindCommentTreeByThread()
    {
        $this->result = array(array('comment' => $this->comment, 'children' => array()));
        $this->realManager->expects($this->once())
             ->method('findCommentTreeByThread')
             ->with($this->equalTo($this->thread),
                   $this->equalTo($this->sorting_strategy),
                   $this->equalTo($this->depth))
             ->will($this->returnValue($this->result));
        $this->configureCommentSecurity('canView', false);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $manager->findCommentTreeByThread($this->thread, $this->sorting_strategy, $this->depth);
    }

    /**
     * @covers AclCommentManager::findCommentsByThread
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindCommentsByThread()
    {
        $this->result = array($this->comment);
        $this->realManager->expects($this->once())
            ->method('findCommentsByThread')
            ->with($this->thread,
                   $this->depth)
            ->will($this->returnValue($this->result));
        $this->configureCommentSecurity('canView', false);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $manager->findCommentsByThread($this->thread, $this->depth);
    }

    /**
     * @covers AclCommentManager::findCommentById
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindCommentById()
    {
        $this->commentId = 123;
        $this->result = $this->comment;

        $this->realManager->expects($this->once())
            ->method('findCommentById')
            ->with($this->commentId)
            ->will($this->returnValue($this->result));

        $this->configureCommentSecurity('canView', false);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $manager->findCommentById($this->commentId);
    }

    /**
     * @covers AclCommentManager:findCommentTreeByCommentId
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindCommentTreeByCommentId()
    {
        $this->commentId = 123;
        $this->result = array(array('comment' => $this->comment, 'children' => array()));

        $this->realManager->expects($this->once())
            ->method('findCommentTreeByCommentId')
            ->with($this->commentId,
                   $this->sorting_strategy)
            ->will($this->returnValue($this->result));

        $this->configureCommentSecurity('canView', false);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $manager->findCommentTreeByCommentId($this->commentId, $this->sorting_strategy);
    }

    protected function addCommentSetup()
    {
        $this->parent = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $this->commentReturnsThread();
    }

    /**
     * @covers AclCommentManager::addComment
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testAddCommentNoReplyPermission()
    {
        $this->addCommentSetup();
        $this->configureCommentSecurity('canReply', false);

        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);
        $manager->addComment($this->comment, $this->parent);
    }

    /**
     * @covers AclCommentManager::addComment
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testAddCommentNoThreadViewPermission()
    {
        $this->addCommentSetup();
        $this->configureThreadSecurity('canView', false);

        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);
        $manager->addComment($this->comment);
    }

    /**
     * @covers AclCommentManager::addComment
     */
    public function testAddComment()
    {
        $this->addCommentSetup();
        $this->configureCommentSecurity('canReply', true);
        $this->configureThreadSecurity('canView', true);
        $this->commentSecurity->expects($this->once())
            ->method('setDefaultAcl')
            ->with($this->comment);

        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);
        $manager->addComment($this->comment, $this->parent);
    }

}