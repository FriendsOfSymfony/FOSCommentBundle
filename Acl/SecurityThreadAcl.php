<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Model\AclInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
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
    protected $objectRetrieval;

    /**
     * The AclProvider.
     *
     * @var MutableAclProviderInterface
     */
    protected $aclProvider;

    /**
     * @var AuthorizationCheckerInterface|SecurityContextInterface
     */
    protected $authorizationChecker;

    /**
     * The FQCN of the Thread object.
     *
     * @var string
     */
    protected $threadClass;

    /**
     * The Class OID for the Thread object.
     *
     * @var ObjectIdentity
     */
    protected $oid;

    /**
     * Constructor.
     *
     * @param AuthorizationCheckerInterface|SecurityContextInterface $authorizationChecker
     * @param ObjectIdentityRetrievalStrategyInterface               $objectRetrieval
     * @param MutableAclProviderInterface                            $aclProvider
     * @param string                                                 $threadClass
     */
    public function __construct($authorizationChecker,
                                ObjectIdentityRetrievalStrategyInterface $objectRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $threadClass
    )
    {
        if (!$authorizationChecker instanceof AuthorizationCheckerInterface && !$authorizationChecker instanceof SecurityContextInterface) {
            throw new \InvalidArgumentException('Argument 1 should be an instance of Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface or Symfony\Component\Security\Core\SecurityContextInterface');
        }

        $this->authorizationChecker = $authorizationChecker;
        $this->objectRetrieval      = $objectRetrieval;
        $this->aclProvider          = $aclProvider;
        $this->threadClass          = $threadClass;
        $this->oid                  = new ObjectIdentity('class', $this->threadClass);
    }

    /**
     * Checks if the Security token is allowed to create a new Thread.
     *
     * @return boolean
     */
    public function canCreate()
    {
        return $this->authorizationChecker->isGranted('CREATE', $this->oid);
    }

    /**
     * Checks if the Security token is allowed to view the specified Thread.
     *
     * @param  ThreadInterface $thread
     * @return boolean
     */
    public function canView(ThreadInterface $thread)
    {
        return $this->authorizationChecker->isGranted('VIEW', $thread);
    }

    /**
     * Checks if the Security token is allowed to edit the specified Thread.
     *
     * @param  ThreadInterface $thread
     * @return boolean
     */
    public function canEdit(ThreadInterface $thread)
    {
        return $this->authorizationChecker->isGranted('EDIT', $thread);
    }

    /**
     * Checks if the Security token is allowed to delete the specified Thread.
     *
     * @param  ThreadInterface $thread
     * @return boolean
     */
    public function canDelete(ThreadInterface $thread)
    {
        return $this->authorizationChecker->isGranted('DELETE', $thread);
    }

    /**
     * Sets the default object Acl entry for the supplied Thread.
     *
     * @param  ThreadInterface $thread
     * @return void
     */
    public function setDefaultAcl(ThreadInterface $thread)
    {
        $objectIdentity = $this->objectRetrieval->getObjectIdentity($thread);
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
        $oid = new ObjectIdentity('class', $this->threadClass);

        try {
            $acl = $this->aclProvider->createAcl($oid);
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
     * `fos:comment:installAces --flush` command
     *
     * @param  AclInterface $acl
     * @param  MaskBuilder  $builder
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

    /**
     * Removes fallback Acl entries for the Thread class.
     *
     * This should be run when uninstalling the ThreadBundle, or when
     * the Class Acl entry end up corrupted.
     *
     * @return void
     */
    public function uninstallFallbackAcl()
    {
        $oid = new ObjectIdentity('class', $this->threadClass);
        $this->aclProvider->deleteAcl($oid);
    }
}
