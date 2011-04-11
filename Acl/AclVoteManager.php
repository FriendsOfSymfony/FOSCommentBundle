<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;

/**
 * Wraps a real implementation of VoteManagerInterface and
 * performs Acl checks with the configured Vote Acl service.
 *
 * @author Tim Nagel <tim@nagel.com.au
 */
class AclVoteManager implements VoteManagerInterface
{
    /**
     * The VoteManager instance to be wrapped with ACL.
     *
     * @var VoteManagerInterface
     */
    private $realManager;

    /**
     * The Vote Acl instance for querying Acls.
     *
     * @var VoteAclInterface
     */
    private $voteAcl;

    /**
     * {@inheritDoc}
     */
    public function __construct(VoteManagerInterface $voteManager, VoteAclInterface $voteAcl)
    {
        $this->realManager = $voteManager;
        $this->voteAcl   = $voteAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function findVoteById($id)
    {
        return $this->findVoteById($id);
    }

    /**
     * {@inheritDoc}
     */
    public function findVoteBy(array $criteria)
    {
        return $this->findVoteBy($criteria);
    }

    /**
     * {@inheritDoc}
     */
    public function findVotesByComment(CommentInterface $comment)
    {
        return $this->findVotesByComment($comment);
    }

    /**
     * {@inheritDoc}
     */
    public function addVote(VoteInterface $vote, CommentInterface $comment)
    {
        $this->voteAcl->canCreate();

        return $this->addVote($vote, $comment);
    }

    /**
     * {@inheritDoc}
     */
    public function createVote()
    {
        $this->voteAcl->canCreate();

        return $this->createVote();
    }


    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }
}
