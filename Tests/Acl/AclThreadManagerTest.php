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

use FOS\CommentBundle\Acl\AclThreadManager;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Tests the functionality provided by Acl\AclThreadManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class AclThreadManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $realManager;
    protected $threadSecurity;
    protected $thread;
    protected $result;

    public function setUp()
    {
        $this->realManager = $this->getMock('FOS\CommentBundle\Model\ThreadManagerInterface');
        $this->threadSecurity = $this->getMock('FOS\CommentBundle\Acl\ThreadAclInterface');
        $this->thread = $this->getMock('FOS\CommentBundle\Model\ThreadInterface');
        $this->result = null;
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindThreadByIdentifier()
    {
        $threadIdentifier = 'hello';
        $this->realManager->expects($this->once())
            ->method('findThreadByIdentifier')
            ->with($threadIdentifier)
            ->will($this->returnValue($this->thread));

        $this->threadSecurity->expects($this->once())
            ->method('canView')
            ->with($this->thread)
            ->will($this->returnValue(false));

        $manager = new AclThreadManager($this->realManager, $this->threadSecurity);
        $manager->findThreadByIdentifier($threadIdentifier);
    }

    public function testFindThreadByIdentifierNotFound()
    {
        $threadIdentifier = 'hello';
        $this->realManager->expects($this->once())
            ->method('findThreadByIdentifier')
            ->with($threadIdentifier)
            ->will($this->returnValue(null));

        $this->threadSecurity->expects($this->never())
            ->method('canView');

        $manager = new AclThreadManager($this->realManager, $this->threadSecurity);
        $this->assertNull($manager->findThreadByIdentifier($threadIdentifier));
    }

    // findThreadBy - permission denied, can result in null, what to do about invalid criteria
    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindThreadBy()
    {
        $conditions = array('identifier' => 123);
        $this->result = $this->thread;

        $this->realManager->expects($this->once())
            ->method('findThreadBy')
            ->with($conditions)
            ->will($this->returnValue($this->result));

        $this->threadSecurity->expects($this->once())
            ->method('canView')
            ->with($this->thread)
            ->will($this->returnValue(false));

        $manager = new AclThreadManager($this->realManager, $this->threadSecurity);
        $manager->findThreadBy($conditions);
    }

    public function testFindThreadByNoResult()
    {
        $conditions = array('identifier' => 123);
        $this->result = null;

        $this->realManager->expects($this->once())
            ->method('findThreadBy')
            ->with($conditions)
            ->will($this->returnValue($this->result));

        $this->threadSecurity->expects($this->never())
            ->method('canView');

        $manager = new AclThreadManager($this->realManager, $this->threadSecurity);
        $this->assertNull($manager->findThreadBy($conditions));
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testFindAllThreads()
    {
        $this->result = array($this->thread);

        $this->realManager->expects($this->once())
            ->method('findAllThreads')
            ->will($this->returnValue($this->result));

        $this->threadSecurity->expects($this->once())
            ->method('canView')
            ->with($this->thread)
            ->will($this->returnValue(false));

        $manager = new AclThreadManager($this->realManager, $this->threadSecurity);
        $manager->findAllThreads();
    }

    /**
     * @expectedException Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function testAddThread()
    {
        $this->realManager->expects($this->never())
            ->method('addThread');

        $this->threadSecurity->expects($this->once())
            ->method('canCreate')
            ->will($this->returnValue(false));

        $manager = new AclThreadManager($this->realManager, $this->threadSecurity);
        $manager->addThread($this->thread);
    }
}