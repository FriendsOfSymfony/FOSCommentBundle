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

use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\CommentManagerInterface;
use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use InvalidArgumentException;

/**
 * Wraps a real implementation of CommentManagerInterface and
 * performs Acl checks with the configured Comment Acl service.
 *
 * @author Tim Nagel <tim@nagel.com.au
 */
class AclCommentManager implements CommentManagerInterface
{
    /**
     * The CommentManager instance to be wrapped with ACL.
     *
     * @var CommentManagerInterface
     */
    private $realManager;

    /**
     * The CommentAcl instance for checking permissions.
     *
     * @var CommentAclInterface
     */
    private $commentAcl;

    /**
     * The ThreadAcl instance for checking permissions.
     *
     * @var ThreadAclInterface
     */
    private $threadAcl;

    /**
     * Constructor.
     *
     * @param CommentManagerInterface $commentManager The concrete CommentManager service
     * @param CommentAclInterface $commentAcl The Comment Acl service
     * @param ThreadAclInterface $threadAcl The Thread Acl service
     */
    public function __construct(CommentManagerInterface $commentManager, CommentAclInterface $commentAcl, ThreadAclInterface $threadAcl)
    {
        $this->realManager = $commentManager;
        $this->commentAcl  = $commentAcl;
        $this->threadAcl   = $threadAcl;
    }

    /**
     * {@inheritDoc}
     *
     * @throws AccessDeniedException
     */
    public function findCommentTreeByThread(ThreadInterface $thread, $sorter = null, $depth = null)
    {
        $comments = $this->realManager->findCommentTreeByThread($thread, $sorter, $depth);

        if (!$this->authorizeViewCommentTree($comments)) {
            throw new AccessDeniedException();
        }

        return $comments;
    }

    /**
     * {@inheritDoc}
     */
    public function findCommentsByThread(ThreadInterface $thread, $depth = nul, $sorterAlias = null)
    {
        $comments = $this->realManager->findCommentsByThread($thread, $depth, $sorterAlias);

        foreach ($comments AS $comment) {
            if (!$this->commentAcl->canView($comment)) {
                throw new AccessDeniedException();
            }
        }

        return $comments;
    }

    /**
     * {@inheritDoc}
     */
    public function findCommentTreeByCommentId($commentId, $sorter = null)
    {
        $comments = $this->realManager->findCommentTreeByCommentId($commentId, $sorter);

        if (!$this->authorizeViewCommentTree($comments)) {
            throw new AccessDeniedException();
        }

        return $comments;
    }

    /**
     * {@inheritDoc}
     */
    public function addComment(CommentInterface $comment, CommentInterface $parent = null)
    {
        if (!$this->threadAcl->canView($comment->getThread())) {
            throw new AccessDeniedException();
        }

        if (!$this->commentAcl->canReply($parent)) {
            throw new AccessDeniedException();
        }

        $this->realManager->addComment($comment, $parent);
        $this->commentAcl->setDefaultAcl($comment);
    }

    /**
     * {@inheritDoc}
     **/
    public function findCommentById($id)
    {
        $comment = $this->realManager->findCommentById($id);

        if (null !== $comment && !$this->commentAcl->canView($comment)) {
            throw new AccessDeniedException();
        }

        return $comment;
    }

    /**
     * {@inheritDoc}
     */
    public function createComment(ThreadInterface $thread, CommentInterface $parent = null)
    {
        return $this->realManager->createComment($thread, $parent);
    }

    /**
     * {@inheritDoc}
     */
    public function getClass()
    {
        return $this->realManager->getClass();
    }

    /**
     * Iterates over a comment tree array and makes sure all comments
     * have appropriate view permissions.
     *
     * @param array $comments A comment tree
     * @return boolean
     */
    protected function authorizeViewCommentTree(array $comments)
    {
        foreach ($comments AS $comment) {
            if (!$this->commentAcl->canView($comment['comment'])) {
                return false;
            }

            if (is_array($comment['children'])) {
                return $this->authorizeViewCommentTree($comment['children']);
            }
        }

        return true;
    }
}
