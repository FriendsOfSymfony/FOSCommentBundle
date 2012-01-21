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
    /**
     * @return mixed unique ID for this comment
     */
    function getId();

    /**
     * @return string name of the comment author
     */
    function getAuthorName();

    /**
     * @return string
     */
    function getBody();

    /**
     * @param string $body
     */
    function setBody($body);

    /**
     * @return DateTime
     */
    function getCreatedAt();

    /**
     * @return ThreadInterface
     */
    function getThread();

    /**
     * @param ThreadInterface $thread
     */
    function setThread(ThreadInterface $thread);

    /**
     * @return CommentInterface
     */
    function getParent();

    /**
     * @param CommentInterface $comment
     */
    function setParent(CommentInterface $comment);

}
