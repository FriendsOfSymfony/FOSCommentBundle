<?php

/**
 * This file is part of the FOS\CommentBundle.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\SecurityIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Implements ACL checking using the Symfony2 Security component
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SecurityThreadAcl implements ThreadAclInterface
{
    /**
     * Used to retrieve ObjectIdentity instances for objects.
     *
     * @var ObjectIdentityRetrievalStrategy
     */
    private $objectRetrieval;

    /**
     * Used to retrieve UserSecurityIdentity instances for users.
     *
     * @var SecurityIdentityRetrievalStrategy
     */
    private $securityRetrieval;

    /**
     * The AclProvider.
     *
     * @var MutableAclProviderInterface
     */
    private $aclProvider;

    /**
     * The current Security Context.
     *
     * @var SecurityContextInterface
     */
    private $securityContext;

    /**
     * The FQCN of the Thread object.
     *
     * @var string
     */
    private $threadClass;

    /**
     * The Class OID for the Thread object.
     *
     * @var ObjectIdentity
     */
    private $oid;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     * @param ObjectIdentityRetrievalStrategy $objectRetrieval
     * @param SecurityIdentityRetrievalStrategy $securityRetrieval
     * @param MutableAclProviderInterface $aclProvider
     * @param string $threadClass
     */
    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategy $objectRetrieval,
                                SecurityIdentityRetrievalStrategy $securityRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $threadClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->securityRetrieval = $securityRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->threadClass       = $threadClass;
        $this->oid               = new ObjectIdentity('class', $this->threadClass);
    }


    /**
     * Checks if the Security token is allowed to create a new Thread.
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
     * Checks if the Security token is allowed to view the supplied Thread.
     *
     * The exception thrown by this method should be handled by the
     * Symfony2 Security component.
     *
     * @throws AccessDeniedException when not allowed.
     * @param ThreadInterface $thread
     * @return void
     */
    public function canView(ThreadInterface $thread)
    {
        if (!$this->securityContext->isGranted('VIEW', $thread)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Checks if the Security token is allowed to edit the supplied Thread.
     *
     * The exception thrown by this method should be handled by the
     * Symfony2 Security component.
     *
     * @throws AccessDeniedException when not allowed.
     * @param ThreadInterface $thread
     * @return void
     */
    public function canEdit(ThreadInterface $thread)
    {
        if (!$this->securityContext->isGranted('EDIT', $thread)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Checks if the Security token is allowed to delete the supplied Thread.
     *
     * The exception thrown by this method should be handled by the
     * Symfony2 Security component.
     *
     * @throws AccessDeniedException when not allowed.
     * @param ThreadInterface $thread
     * @return void
     */
    public function canDelete(ThreadInterface $thread)
    {
        if (!$this->securityContext->isGranted('DELETE', $thread)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * Sets the default object Acl entry for the supplied Thread.
     *
     * @param ThreadInterface $thread
     * @return void
     */
    public function setDefaultAcl(ThreadInterface $thread)
    {
        $objectIdentity = new ObjectIdentity($thread->getIdentifier(), get_class($thread));
        $acl = $this->aclProvider->createAcl($objectIdentity);
        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs default Acl entries for the Thread class.
     *
     * This needs to be re-run whenever the Thread class changes or is subclassed.
     *
     * @return void
     */
    public function installFallbackAcl()
    {
        try {
            $acl = $this->aclProvider->createAcl($this->oid);
        } catch (AclAlreadyExistsException $exists) {
            return;
        }

        $this->doInstallFallbackAcl($acl, new MaskBuilder());
        $this->aclProvider->updateAcl($acl);
    }

    /**
     * Installs the default Class Ace entries into the provided $acl object.
     *
     * Override this method in a subclass to change what permissions are defined.
     * Once this method has been overridden you need to run the
     * `fos_comment:installAces --flush` command
     *
     * @param AclInterface $acl
     * @param MaskBuilder $builder
     * @return void
     */
    protected function doInstallFallbackAcl(AclInterface $acl, MaskBuilder $builder)
    {
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
    }

    /**
     * Removes fallback Acl entries for the Thread class.
     *
     * This should be run when uninstalling the CommentBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $this->aclProvider->deleteAcl($this->oid);
    }
}
