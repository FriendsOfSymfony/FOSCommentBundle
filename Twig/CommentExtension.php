<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Twig;

use FOS\CommentBundle\Acl\CommentAclInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\RawCommentInterface;
use FOS\CommentBundle\Acl\ThreadAclInterface;
use FOS\CommentBundle\Acl\VoteAclInterface;

/**
 * Extends Twig to provide some helper functions for the CommentBundle.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentExtension extends \Twig_Extension
{
    protected $commentAcl;
    protected $voteAcl;
    protected $threadAcl;

    public function __construct(CommentAclInterface $commentAcl = null, VoteAclInterface $voteAcl = null, ThreadAclInterface $threadAcl = null)
    {
        $this->commentAcl = $commentAcl;
        $this->voteAcl    = $voteAcl;
        $this->threadAcl  = $threadAcl;
    }

    public function getTests()
    {
        return array(
            'fos_comment_deleted'         => new \Twig_Test_Method($this, 'isCommentDeleted'),
            'fos_comment_in_state'        => new \Twig_Test_Method($this, 'isCommentInState'),
            'fos_comment_votable'         => new \Twig_Test_Method($this, 'isVotable'),
            'fos_comment_raw'             => new \Twig_Test_Method($this, 'isRawComment'),
        );
    }

    /**
     * Check if the state of the comment is deleted.
     *
     * @param CommentInterface $comment
     *
     * @return bool
     *
     * @deprecated Use isCommentInState instead.
     */
    public function isCommentDeleted(CommentInterface $comment)
    {
        return $this->isCommentInState($comment, $comment::STATE_DELETED);
    }

    /**
     * Checks if comment is in given state.
     *
     * @param CommentInterface $comment
     * @param int              $state   CommentInterface::STATE_*
     *
     * @return bool
     */
    public function isCommentInState(CommentInterface $comment, $state)
    {
        return $comment->getState() === $state;
    }

    /**
     * Checks if the comment is an instance of a VotableCommentInterface.
     *
     * @param mixed The value to check for VotableCommentInterface
     * @return bool If $value implements VotableCommentInterface
     */
    public function isVotable($value)
    {
        return ($value instanceof VotableCommentInterface);
    }

    public function isRawComment($comment)
    {
        return ($comment instanceof RawCommentInterface);
    }

    public function getFunctions()
    {
        return array(
            'fos_comment_can_comment'        => new \Twig_Function_Method($this, 'canComment'),
            'fos_comment_can_vote'           => new \Twig_Function_Method($this, 'canVote'),
            'fos_comment_can_delete_comment' => new \Twig_Function_Method($this, 'canDeleteComment'),
            'fos_comment_can_edit_comment'   => new \Twig_Function_Method($this, 'canEditComment'),
            'fos_comment_can_edit_thread'    => new \Twig_Function_Method($this, 'canEditThread'),
            'fos_comment_can_comment_thread' => new \Twig_Function_Method($this, 'canCommentThread'),
        );
    }

    /*
     * Checks if the current user is able to comment. Checks if they
     * can create root comments if no $comment is provided, otherwise
     * checks if they can reply to a given comment if supplied.
     *
     * @param  CommentInterface|null $comment
     * @return bool                  If the user is able to comment
     */
    public function canComment(CommentInterface $comment = null)
    {
        if (null !== $comment
            && null !== $comment->getThread()
            && !$comment->getThread()->isCommentable()) {
            return false;
        }

        if (null === $this->commentAcl) {
            return true;
        }

        if (null === $comment) {
            return $this->commentAcl->canCreate();
        }

        return $this->commentAcl->canReply($comment);
    }

    /**
     * Checks if the current user is able to delete a comment.
     *
     * @param CommentInterface $comment
     *
     * @return bool
     */
    public function canDeleteComment(CommentInterface $comment)
    {
        if (null === $this->commentAcl) {
            return false;
        }

        return $this->commentAcl->canDelete($comment);
    }

    /**
     * Checks if the current user is able to edit a comment.
     *
     * @param CommentInterface $comment
     *
     * @return bool If the user is able to comment
     */
    public function canEditComment(CommentInterface $comment)
    {
        if (!$comment->getThread()->isCommentable()) {
            return false;
        }

        if (null === $this->commentAcl) {
            return false;
        }

        return $this->commentAcl->canEdit($comment);
    }

    /**
     * Checks if the comment is Votable and that the user has
     * permission to vote.
     *
     * @param  \FOS\CommentBundle\Model\CommentInterface $comment
     * @return bool
     */
    public function canVote(CommentInterface $comment)
    {
        if (!$comment instanceof VotableCommentInterface) {
            return false;
        }

        if (null === $this->voteAcl) {
            return true;
        }

        if (null !== $this->commentAcl && !$this->commentAcl->canView($comment)) {
            return false;
        }

        return $this->voteAcl->canCreate();
    }

    /**
     * Checks if the thread can be edited.
     *
     * Will use the specified ACL, or return true otherwise.
     *
     * @param ThreadInterface $thread
     *
     * @return bool
     */
    public function canEditThread(ThreadInterface $thread)
    {
        if (null === $this->threadAcl) {
            return false;
        }

        return $this->threadAcl->canEdit($thread);
    }

    /**
     * Checks if the thread can be commented.
     *
     * @param ThreadInterface $thread
     *
     * @return bool
     */
    public function canCommentThread(ThreadInterface $thread)
    {
        return $thread->isCommentable()
            && (null === $this->commentAcl || $this->commentAcl->canCreate());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'fos_comment';
    }
}
