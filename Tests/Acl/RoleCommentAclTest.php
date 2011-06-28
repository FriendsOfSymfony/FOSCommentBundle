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

use FOS\CommentBundle\Acl\AclVoteManager;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Tests the functionality provided by Acl\AclVoteManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class RoleCommentAclTest extends \PHPUnit_Framework_TestCase
{
    protected $roleCommentAcl;
    protected $createRole = 'ROLE_CREATE';
    protected $viewRole = 'ROLE_VIEW';
    protected $replyRole = 'ROLE_REPLY';
    protected $editRole = 'ROLE_EDIT';
    protected $deleteRole = 'ROLE_DELETE';

    //create
    //view
    //reply
    //edit
    //delete

    public function setup()
    {

    }

    public function testRoles()
    {


    }




}