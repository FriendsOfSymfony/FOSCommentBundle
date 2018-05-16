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

use FOS\CommentBundle\Model\VoteInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Implements Role checking using the Symfony Security component.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class RoleVoteAcl implements VoteAclInterface
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * The FQCN of the Vote object.
     *
     * @var string
     */
    private $voteClass;

    /**
     * The role that will grant create permission for a vote.
     *
     * @var string
     */
    private $createRole;

    /**
     * The role that will grant view permission for a vote.
     *
     * @var string
     */
    private $viewRole;

    /**
     * The role that will grant edit permission for a vote.
     *
     * @var string
     */
    private $editRole;

    /**
     * The role that will grant delete permission for a vote.
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
     * @param string                        $voteClass
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker,
                                $createRole,
                                $viewRole,
                                $editRole,
                                $deleteRole,
                                $voteClass
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->createRole = $createRole;
        $this->viewRole = $viewRole;
        $this->editRole = $editRole;
        $this->deleteRole = $deleteRole;
        $this->voteClass = $voteClass;
    }

    /**
     * Checks if the Security token has an appropriate role to create a new Vote.
     *
     * @return bool
     */
    public function canCreate()
    {
        return $this->authorizationChecker->isGranted($this->createRole);
    }

    /**
     * Checks if the Security token is allowed to view the specified Vote.
     *
     * @param VoteInterface $vote
     *
     * @return bool
     */
    public function canView(VoteInterface $vote)
    {
        return $this->authorizationChecker->isGranted($this->viewRole);
    }

    /**
     * Checks if the Security token has an appropriate role to edit the supplied Vote.
     *
     * @param VoteInterface $vote
     *
     * @return bool
     */
    public function canEdit(VoteInterface $vote)
    {
        return $this->authorizationChecker->isGranted($this->editRole);
    }

    /**
     * Checks if the Security token is allowed to delete a specific Vote.
     *
     * @param VoteInterface $vote
     *
     * @return bool
     */
    public function canDelete(VoteInterface $vote)
    {
        return $this->authorizationChecker->isGranted($this->deleteRole);
    }

    /**
     * Role based Acl does not require setup.
     *
     * @param VoteInterface $vote
     *
     * @return void
     */
    public function setDefaultAcl(VoteInterface $vote)
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
