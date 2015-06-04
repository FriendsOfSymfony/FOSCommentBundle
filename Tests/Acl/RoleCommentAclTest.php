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

use FOS\CommentBundle\Acl\RoleCommentAcl;

/**
 * Tests the functionality provided by Acl\AclVoteManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class RoleCommentAclTest extends AbstractRoleAcl
{
    public function setUp()
    {
        parent::setUp();

        $this->roleAcl = new RoleCommentAcl($this->authorizationChecker,
            $this->createRole,
            $this->viewRole,
            $this->editRole,
            $this->deleteRole,
            '');
        $this->passObject = $this->getMock('FOS\CommentBundle\Model\CommentInterface');
    }

    public function getRoles()
    {
        return array_merge(parent::getRoles(), array(
            array('reply')
        ));
    }
}
