<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\ObjectIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Domain\SecurityIdentityRetrievalStrategy;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
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
    private $objectRetrieval;
    private $securityRetrieval;
    private $aclProvider;
    private $securityContext;
    private $threadClass;

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
    }

    protected $oid;
    protected function getOid()
    {
        if (!$this->oid) {
            $this->oid = new ObjectIdentity('class', $this->threadClass);
        }

        return $this->oid;
    }

    public function canCreate()
    {
        if (!$this->securityContext->isGranted('CREATE', $this->getOid())) {
            throw new AccessDeniedException();
        }
    }

    public function canView(ThreadInterface $thread)
    {
        if (!$this->securityContext->isGranted('VIEW', $thread)) {
            throw new AccessDeniedException();
        }
    }

    public function canEdit(ThreadInterface $thread)
    {
        if (!$this->securityContext->isGranted('EDIT', $thread)) {
            throw new AccessDeniedException();
        }
    }

    public function canDelete(ThreadInterface $thread)
    {
        if (!$this->securityContext->isGranted('DELETE', $thread)) {
            throw new AccessDeniedException();
        }
    }

    public function setDefaultAcl(ThreadInterface $thread)
    {
        $objectIdentity = new ObjectIdentity($thread->getIdentifier(), get_class($thread));
        $acl = $this->aclProvider->createAcl($objectIdentity);
        $this->aclProvider->updateAcl($acl);
    }
}
