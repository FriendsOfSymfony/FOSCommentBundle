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

/*
 * Binds a comment tree to anything, using a unique, arbitrary id
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface ThreadInterface
{
    /**
     * Id, a unique string that binds the comments together in a thread (tree).
     * It can be a url or really anything unique.
     *
     * @return string
     */
    function getId();

    /**
     * @param string
     */
    function setId($id);

    /**
     * Url of the page where the thread lives
     * @return string
     */
    function getPermalink();

    /**
     * @param  string
     * @return null
     */
    function setPermalink($permalink);

    /**
     * Tells if new comments can be added in this thread
     *
     * @return bool
     */
    function isCommentable();

    /**
     * @param bool $isCommentable
     */
    function setIsCommentable($isCommentable);

    /**
     * Gets the number of comments
     *
     * @return integer
     */
    function getNumComments();

    /**
     * Sets the number of comments
     *
     * @param integer $numComments
     */
    function setNumComments($numComments);

    /**
     * Increments the number of comments by the supplied
     * value.
     *
     * @param integer $by The number of comments to increment by
     * @return integer The new comment total
     */
    function incrementNumComments($by);

    /**
     * Denormalized date of the last comment
     * @return DateTime
     */
    function getLastCommentAt();

    /**
     * @param  DateTime
     * @return null
     */
     function setLastCommentAt($lastCommentAt);
}
