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
            'fos_comment_votable'         => new \Twig_Test_Method($this, 'isVotable'),
            'fos_comment_raw'             => new \Twig_Test_Method($this, 'isRawComment'),
        );
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
            'fos_comment_can_comment'     => new \Twig_Function_Method($this, 'canComment'),
            'fos_comment_can_vote'        => new \Twig_Function_Method($this, 'canVote'),
            'fos_comment_can_edit_thread' => new \Twig_Function_Method($this, 'canEditThread'),
        );
    }

    /*
     * Checks if the current user is able to comment. Checks if they
     * can create root comments if no $comment is provided, otherwise
     * checks if they can reply to a given comment if supplied.
     *
     * @param CommentInterface|null $comment
     * @return bool If the user is able to comment
     */
    public function canComment(CommentInterface $comment = null)
    {
        if (null === $this->commentAcl) {
            return true;
        }

        if (null === $comment) {
            return $this->commentAcl->canCreate();
        }

        return $this->commentAcl->canReply($comment);
    }

    /**
     * Checks if the comment is Votable and that the user has
     * permission to vote.
     *
     * @param \FOS\CommentBundle\Model\CommentInterface $comment
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
            return true;
        }

        return $this->threadAcl->canEdit($thread);
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
