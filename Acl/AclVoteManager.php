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

use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * The Comment Acl for querying Acls.
     *
     * @var CommentAclInterface
     */
    private $commentAcl;

    /**
     * {@inheritDoc}
     */
    public function __construct(VoteManagerInterface $voteManager, VoteAclInterface $voteAcl, CommentAclInterface $commentAcl)
    {
        $this->realManager = $voteManager;
        $this->voteAcl = $voteAcl;
        $this->commentAcl = $commentAcl;
    }

    /**
     * {@inheritDoc}
     */
    public function findVoteById($id)
    {
        $vote = $this->realManager->findVoteById($id);

        if (!$this->voteAcl->canView($vote)) {
            throw new AccessDeniedException();
        }

        return $vote;
    }

    /**
     * {@inheritDoc}
     */
    public function findVoteBy(array $criteria)
    {
        $vote = $this->realManager->findVoteBy($criteria);

        if (!$this->voteAcl->canView($vote)) {
            throw new AccessDeniedException();
        }

        return $vote;
    }

    /**
     * {@inheritDoc}
     */
    public function findVotesByComment(VotableCommentInterface $comment)
    {
        $votes = $this->realManager->findVotesByComment($comment);

        foreach ($votes as $vote) {
            if (!$this->voteAcl->canView($vote)) {
                throw new AccessDeniedException();
            }
        }

        return $votes;
    }

    /**
     * {@inheritDoc}
     */
    public function addVote(VoteInterface $vote, VotableCommentInterface $comment)
    {
        if (!$this->voteAcl->canCreate()) {
            throw new AccessDeniedException();
        }

        if (!$this->commentAcl->canView($comment)) {
            throw new AccessDeniedException();
        }

        $this->realManager->addVote($vote, $comment);
    }

    /**
     * {@inheritDoc}
     */
    public function createVote()
    {
        return $this->realManager->createVote();
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }
}
