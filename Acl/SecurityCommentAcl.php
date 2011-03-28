<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\SignedCommentInterface;
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
class SecurityCommentAcl implements CommentAclInterface
{
    private $objectRetrieval;
    private $securityRetrieval;
    private $aclProvider;
    private $securityContext;
    private $commentClass;

    public function __construct(SecurityContextInterface $securityContext,
                                ObjectIdentityRetrievalStrategy $objectRetrieval,
                                SecurityIdentityRetrievalStrategy $securityRetrieval,
                                MutableAclProviderInterface $aclProvider,
                                $commentClass
    )
    {
        $this->objectRetrieval   = $objectRetrieval;
        $this->securityRetrieval = $securityRetrieval;
        $this->aclProvider       = $aclProvider;
        $this->securityContext   = $securityContext;
        $this->commentClass      = $commentClass;
    }

    protected $oid;
    protected function getOid()
    {
        if (!$this->oid) {
            $this->oid = new ObjectIdentity('class', $this->commentClass);
        }

        return $this->oid;
    }

    public function canCreate()
    {
        if (!$this->securityContext->isGranted('CREATE', $this->getOid())) {
            throw new AccessDeniedException();
        }
    }

    public function canView(CommentInterface $comment)
    {
        if (!$this->securityContext->isGranted('VIEW', $comment)) {
            throw new AccessDeniedException();
        }
    }

    public function canEdit(CommentInterface $comment)
    {
        if (!$this->securityContext->isGranted('EDIT', $comment)) {
            throw new AccessDeniedException();
        }
    }

    public function canDelete(CommentInterface $comment)
    {
        if (!$this->securityContext->isGranted('DELETE', $comment)) {
            throw new AccessDeniedException();
        }
    }

    public function setDefaultAcl(CommentInterface $comment)
    {
        $objectIdentity   = $this->objectRetrieval->getObjectIdentity($comment);
        $acl = $this->aclProvider->createAcl($objectIdentity);

        if ($comment instanceof SignedCommentInterface) {
            $securityIdentity = UserSecurityIdentity::fromAccount($comment->getAuthor());
            $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OWNER);
        }

        $this->aclProvider->updateAcl($acl);
    }
}
