<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Implements Role checking using the Symfony Security component.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class RoleThreadAcl implements ThreadAclInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * The FQCN of the Thread object.
     *
     * @var string
     */
    private $threadClass;

    /**
     * The role that will grant create permission for a thread.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a thread.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a thread.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a thread.
     *
     * @var string
     */
    private $deleteRole;

    /**
     * Constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param string                        $createRole
     * @param string                        $viewRole
     * @param string                        $editRole
     * @param string                        $deleteRole
     * @param string                        $threadClass
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $threadClass
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->createRole = $createRole;
        $this->viewRole = $viewRole;
        $this->editRole = $editRole;
        $this->deleteRole = $deleteRole;
        $this->threadClass = $threadClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Thread.
     *
     * @return bool
     */
    public function canCreate()
    {
        return $this->authorizationChecker->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Thread.
     *
     * @param ThreadInterface $thread
     *
     * @return bool
     */
    public function canView(ThreadInterface $thread)
    {
        return $this->authorizationChecker->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Thread.
     *
     * @param ThreadInterface $thread
     *
     * @return bool
     */
    public function canEdit(ThreadInterface $thread)
    {
        return $this->authorizationChecker->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Thread.
     *
     * @param ThreadInterface $thread
     *
     * @return bool
     */
    public function canDelete(ThreadInterface $thread)
    {
        return $this->authorizationChecker->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param ThreadInterface $thread
     *
     * @return void
     */
    public function setDefaultAcl(ThreadInterface $thread)
    {
    }

    /**
     * Role based Acl does not require setup.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
    }

    /**
     * Role based Acl does not require setup.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
    }
}
