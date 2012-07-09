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

    public function testFindCommentTreeByThreadNestedResult()
    {
        $expectedResult = array(
            array('comment' => $this->comment, 'children' => array(
                array('comment' => $this->comment, 'children' => array()),
                array('comment' => $this->comment, 'children' => array())
            ))
        );

        $this->realManager->expects($this->once())
             ->method('findCommentTreeByThread')
             ->with($this->equalTo($this->thread),
                   $this->equalTo($this->sorting_strategy),
                   $this->equalTo($this->depth))
             ->will($this->returnValue($expectedResult));
        $this->configureCommentSecurity('canView', true);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $result = $manager->findCommentTreeByThread($this->thread, $this->sorting_strategy, $this->depth);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindCommentTreeByThread()
    {
        $expectedResult = array(array('comment' => $this->comment, 'children' => array()));
        $this->realManager->expects($this->once())
             ->method('findCommentTreeByThread')
             ->with($this->equalTo($this->thread),
                   $this->equalTo($this->sorting_strategy),
                   $this->equalTo($this->depth))
             ->will($this->returnValue($expectedResult));
        $this->configureCommentSecurity('canView', false);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $manager->findCommentTreeByThread($this->thread, $this->sorting_strategy, $this->depth);
    }

    public function testFindCommentsByThreadCanView()
    {
        $expectedResult = array($this->comment);
        $this->realManager->expects($this->once())
            ->method('findCommentsByThread')
            ->with($this->thread,
                   $this->depth)
            ->will($this->returnValue($expectedResult));
        $this->configureCommentSecurity('canView', true);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $result = $manager->findCommentsByThread($this->thread, $this->depth);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindCommentsByThread()
    {
        $expectedResult = array($this->comment);
        $this->realManager->expects($this->once())
            ->method('findCommentsByThread')
            ->with($this->thread,
                   $this->depth)
            ->will($this->returnValue($expectedResult));
        $this->configureCommentSecurity('canView', false);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $manager->findCommentsByThread($this->thread, $this->depth);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindCommentById()
    {
        $commentId = 123;
        $expectedResult = $this->comment;

        $this->realManager->expects($this->once())
            ->method('findCommentById')
            ->with($commentId)
            ->will($this->returnValue($expectedResult));

        $this->configureCommentSecurity('canView', false);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $manager->findCommentById($commentId);
    }

    public function testFindCommentByIdCanView()
    {
        $commentId = 123;
        $expectedResult = $this->comment;

        $this->realManager->expects($this->once())
            ->method('findCommentById')
            ->with($commentId)
            ->will($this->returnValue($expectedResult));

        $this->configureCommentSecurity('canView', true);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $result = $manager->findCommentById($commentId);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindCommentTreeByCommentId()
    {
        $commentId = 123;
        $expectedResult = array(array('comment' => $this->comment, 'children' => array()));

        $this->realManager->expects($this->once())
            ->method('findCommentTreeByCommentId')
            ->with($commentId,
                   $this->sorting_strategy)
            ->will($this->returnValue($expectedResult));

        $this->configureCommentSecurity('canView', false);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $manager->findCommentTreeByCommentId($commentId, $this->sorting_strategy);
    }

    public function testFindCommentTreeByCommentIdCanView()
    {
        $commentId = 123;
        $expectedResult = array(array('comment' => $this->comment, 'children' => array()));

        $this->realManager->expects($this->once())
            ->method('findCommentTreeByCommentId')
            ->with($commentId,
                   $this->sorting_strategy)
            ->will($this->returnValue($expectedResult));

        $this->configureCommentSecurity('canView', true);
        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);

        $result = $manager->findCommentTreeByCommentId($commentId, $this->sorting_strategy);
        $this->assertEquals($expectedResult, $result);
    }

    protected function saveCommentSetup()
    {
        $this->parent = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
        $this->commentReturnsThread();
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testSaveCommentNoReplyPermission()
    {
        $this->saveCommentSetup();
        $this->configureThreadSecurity('canView', true);
        $this->configureCommentSecurity('canReply', false);

        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);
        $manager->saveComment($this->comment, $this->parent);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testSaveCommentNoThreadViewPermission()
    {
        $this->saveCommentSetup();
        $this->configureThreadSecurity('canView', false);

        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);
        $manager->saveComment($this->comment);
    }

    public function testSaveComment()
    {
        $this->saveCommentSetup();
        $this->configureCommentSecurity('canReply', true);
        $this->configureThreadSecurity('canView', true);
        $this->commentSecurity->expects($this->once())
            ->method('setDefaultAcl')
            ->with($this->comment);

        $this->realManager->expects($this->once())
             ->method('isNewComment')
             ->with($this->equalTo($this->comment))
             ->will($this->returnValue(true));

        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);
        $manager->saveComment($this->comment, $this->parent);
    }

    protected function editCommentSetup()
    {
        $this->saveCommentSetup();
        $this->configureCommentSecurity('canReply', true);
        $this->configureThreadSecurity('canView', true);

        $this->realManager->expects($this->once())
             ->method('isNewComment')
             ->with($this->equalTo($this->comment))
             ->will($this->returnValue(false));
    }

    public function testSaveEditedComment()
    {
        $this->editCommentSetup();
        $this->configureCommentSecurity('canEdit', true);
        $this->commentSecurity->expects($this->never())
            ->method('setDefaultAcl');

        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);
        $manager->saveComment($this->comment, $this->parent);
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testSaveEditedCommentNoEditPermission()
    {
        $this->editCommentSetup();
        $this->configureCommentSecurity('canEdit', false);

        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);
        $manager->saveComment($this->comment);
    }

    public function testCreateComment()
    {
        $this->parent = $this->getMock('FOS\CommentBundle\Model\CommentInterface');

        $this->realManager->expects($this->once())
            ->method('createComment')
            ->with($this->thread,
                   $this->parent)
            ->will($this->returnValue($this->comment));

        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);
        $return = $manager->createComment($this->thread, $this->parent);

        $this->assertEquals($this->comment, $return);
    }

    public function testGetClass()
    {
        $class = 'Test\\Class';

        $this->realManager->expects($this->once())
            ->method('getClass')
            ->will($this->returnValue($class));

        $manager = new AclCommentManager($this->realManager, $this->commentSecurity, $this->threadSecurity);
        $result = $manager->getClass();

        $this->assertEquals($class, $result);
    }
}
