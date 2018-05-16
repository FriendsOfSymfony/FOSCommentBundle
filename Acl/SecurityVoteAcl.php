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

use FOS\CommentBundle\Model\SignedVoteInterface;
use FOS\CommentBundle\Model\VoteInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Implements ACL checking using the Symfony Security component.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SecurityVoteAcl implements VoteAclInterface
{
    /**
     * Used to retrieve ObjectIdentity instances for objects.
     *
     * @var ObjectIdentityRetrievalStrategy
     */
    protected $objectRetrieval;

    /**
     * The AclProvider.
     *
     * @var MutableAclProviderInterface
     */
    protected $aclProvider;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * The FQCN of the Vote object.
     *
     * @var string
     */
    protected $voteClass;

    /**
     * The Class OID for the Vote object.
     *
     * @var ObjectIdentity
     */
    protected $oid;

    /**
     * Constructor.
     *
     * @param AuthorizationCheckerInterface            $authorizationChecker
     * @param ObjectIdentityRetrievalStrategyInterface $objectRetrieval
     * @param MutableAclProviderInterface              $aclProvider
     * @param string                                   $voteClass
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $voteClass
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->objectRetrieval = $objectRetrieval;
        $this->aclProvider = $aclProvider;
        $this->voteClass = $voteClass;
        $this->oid = new ObjectIdentity('class', $this->voteClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Vote.
     *
     * @return bool
     */
    public function canCreate()
    {
        return $this->authorizationChecker->isGranted('CREATE', $this->oid);
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
        return $this->authorizationChecker->isGranted('VIEW', $vote);
    }

    /**
     * Checks if the Security token is allowed to edit the specified Vote.
     *
     * @param VoteInterface $vote
     *
     * @return bool
     */
    public function canEdit(VoteInterface $vote)
    {
        return $this->authorizationChecker->isGranted('EDIT', $vote);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Vote.
     *
     * @param VoteInterface $vote
     *
     * @return bool
     */
    public function canDelete(VoteInterface $vote)
    {
        return $this->authorizationChecker->isGranted('DELETE', $vote);
    }

    /**
     * Sets the default object Acl entry for the supplied Vote.
     *
     * @param VoteInterface $vote
     *
     * @return void
     */
    public function setDefaultAcl(VoteInterface $vote)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($vote);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($vote instanceof SignedVoteInterface &&
            null !== $vote->getVoter()) {
            $securityIdentity = UserSecurityIdentity::fromAccount($vote->getVoter());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Vote class.
     *
     * This needs to be re-run whenever the Vote class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->voteClass);

        try {
            $acl = $this->aclProvider->createAcl($oid);
        } catch (AclAlreadyExistsException $exists) {
            return;
        }

        $this->doInstallFallbackAcl($acl, new MaskBuilder());
        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Removes fallback Acl entries for the Vote class.
     *
     * This should be run when uninstalling the VoteBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->voteClass);
        $this->aclProvider->deleteAcl($oid);
    }

    /**
     * Installs the default Class Ace entries into the provided $acl object.
     *
     * Override this method in a subclass to change what permissions are defined.
     * Once this method has been overridden you need to run the
     * `fos_vote:installAces --flush` command
     *
     * @param AclInterface $acl
     * @param MaskBuilder  $builder
     *
     * @return void
     */
    protected function doInstallFallbackAcl(AclInterface $acl, MaskBuilder $builder)
    {
        $builder->add('iddqd');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_SUPER_ADMIN'), $builder->get());

        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'), $builder->get());

        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_USER'), $builder->get());
    }
}
