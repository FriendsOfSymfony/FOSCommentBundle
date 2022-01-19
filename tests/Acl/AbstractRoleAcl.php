<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Acl;

use PHPUnit\Framework\TestCase;

/**
 * Abstract functionality for the Role*Acl test classes.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
abstract class AbstractRoleAcl extends TestCase
{
    protected $authorizationChecker;
    protected $roleAcl;
    protected $passObject;

    protected $createRole = 'ROLE_CREATE';
    protected $viewRole = 'ROLE_VIEW';
    protected $editRole = 'ROLE_EDIT';
    protected $deleteRole = 'ROLE_DELETE';

    public function setUp(): void
    {
        $this->authorizationChecker = $this->getMockBuilder('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')->getMock();
    }

    public function getRoles()
    {
        return [
            ['create'],
            ['view'],
            ['edit'],
            ['delete'],
        ];
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
