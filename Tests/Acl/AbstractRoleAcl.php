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
    protected $securityContext;
    protected $roleAcl;
    protected $passObject;

    protected $createRole = 'ROLE_CREATE';
    protected $viewRole = 'ROLE_VIEW';
    protected $editRole = 'ROLE_EDIT';
    protected $deleteRole = 'ROLE_DELETE';

    public function setup()
    {
        $this->securityContext = $this->getMock('Symfony\Component\Security\Core\SecurityContextInterface');
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
        $this->securityContext->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(true));

        if ('create' === $role) {
            $result = $this->roleAcl->{"can{$role}"}();
        } else {
            $result = $this->roleAcl->{"can{$role}"}($this->passObject);
        }
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getRoles
     */
    public function testRolesFailure($role)
    {
        $this->securityContext->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(false));

        if ('create' === $role) {
            $result = $this->roleAcl->{"can{$role}"}();
        } else {
            $result = $this->roleAcl->{"can{$role}"}($this->passObject);
        }
        $this->assertFalse($result);
    }
}
