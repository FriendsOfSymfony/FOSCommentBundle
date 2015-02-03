<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

/**
 * CommentInterface.
 *
 * Any comment to be used by FOS\CommentBundle must implement this interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface CommentInterface
{
    const STATE_VISIBLE = 0;

    const STATE_DELETED = 1;

    const STATE_SPAM = 2;

    const STATE_PENDING = 3;

    /**
     * @return mixed unique ID for this comment
     */
    public function getId();

    /**
     * @return string name of the comment author
     */
    public function getAuthorName();

    /**
     * @return string
     */
    public function getBody();

    /**
     * @param string $body
     */
    public function setBody($body);

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return ThreadInterface
     */
    public function getThread();

    /**
     * @param ThreadInterface $thread
     */
    public function setThread(ThreadInterface $thread);

    /**
     * @return CommentInterface
     */
    public function getParent();

    /**
     * @param CommentInterface $comment
     */
    public function setParent(CommentInterface $comment);

    /**
     * @return integer The current state of the comment
     */
    public function getState();

    /**
     * @param integer $state
     */
    public function setState($state);

    /**
     * Gets the previous state.
     *
     * @return integer
     */
    public function getPreviousState();
}
