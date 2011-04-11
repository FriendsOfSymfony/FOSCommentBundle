<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\SignedVoteInterface;
use FOS\CommentBundle\Model\VoteInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\SecurityIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements ACL checking using the Symfony2 Security component
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SecurityVoteAcl implements VoteAclInterface
{
    private $objectRetrieval;
    private $securityRetrieval;
    private $aclProvider;
    private $securityContext;
    private $voteClass;
    private $oid;

    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategy $objectRetrieval,
                                SecurityIdentityRetrievalStrategy $securityRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $voteClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->securityRetrieval = $securityRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->voteClass         = $voteClass;
    }

    /**
     * Creates the Class ObjectIdentity instance
     *
     * @return ObjectIdentity
     */
    protected function getOid()
    {
        if (!$this->oid) {
            $this->oid = new ObjectIdentity('class', $this->voteClass);
        }

        return $this->oid;
    }

    /**
     * Checks if the Security token is allowed to create a new Vote.
     *
     * The exception thrown by this method should be handled by the
     * Symfony2 Security component.
     *
     * @throws AccessDeniedException when not allowed.
     * @return void
     */
    public function canCreate()
    {
        if (!$this->securityContext->isGranted('CREATE', $this->getOid())) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Checks if the Security token is allowed to delete a specific Vote.
     *
     * The exception thrown by this method should be handled by the
     * Symfony2 Security component.
     *
     * @throws AccessDeniedException when not allowed.
     * @return void
     */
    public function canDelete(VoteInterface $vote)
    {
        if (!$this->securityContext->isGranted('DELETE', $vote)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Sets the default object Acl entry for the supplied Vote.
     *
     * @param VoteInterface $vote
     * @return void
     */
    public function setDefaultAcl(VoteInterface $vote)
    {
        $objectIdentity = new ObjectIdentity($vote->getId(), get_class($vote));
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($vote instanceof SignedVoteInterface) {
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

        $builder = new MaskBuilder();

        $builder->add('iddqd');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_SUPERADMIN'), $builder->get());

        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'), $builder->get());

        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_USER'), $builder->get());

        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Removes fallback Acl entries for the vote Class.
     *
     * This should be run when uninstalling the CommentBundle, or when
     * the Acl entries end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->voteClass);
        $this->aclProvider->deleteAcl($oid);
    }
}
