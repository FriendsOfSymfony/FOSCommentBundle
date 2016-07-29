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

/**
 * Abstract functionality for the Role*Acl test classes.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
abstract class AbstractRoleAcl extends \PHPUnit_Framework_TestCase
{
    protected $authorizationChecker;
    protected $roleAcl;
    protected $passObject;

    protected $createRole = 'ROLE_CREATE';
    protected $viewRole = 'ROLE_VIEW';
    protected $editRole = 'ROLE_EDIT';
    protected $deleteRole = 'ROLE_DELETE';

    public function setUp()
    {
        if (interface_exists('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')) {
            $this->authorizationChecker = $this->getMockBuilder('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')->getMock();
        } else {
            $this->authorizationChecker = $this->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')->getMock();
        }
    }

    public function getRoles()
    {
        return array(
            array('create'),
            array('view'),
            array('edit'),
            array('delete')
        );
    }

    /**
     * @dataProvider getRoles
     */
    public function testRoles($role)
    {
        $this->authorizationChecker->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(true));

        $result = $this->roleAcl->{"can{$role}"}($this->passObject);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getRoles
     */
    public function testRolesFailure($role)
    {
        $this->authorizationChecker->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(false));

        $result = $this->roleAcl->{"can{$role}"}($this->passObject);
        $this->assertFalse($result);
    }
}
