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

use DateTime;

/**
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
    public function getId();

    /**
     * @param string
     */
    public function setId($id);

    /**
     * Url of the page where the thread lives
     * @return string
     */
    public function getPermalink();

    /**
     * @param  string
     * @return null
     */
    public function setPermalink($permalink);

    /**
     * Tells if new comments can be added in this thread
     *
     * @return bool
     */
    public function isCommentable();

    /**
     * @param bool $isCommentable
     */
    public function setCommentable($isCommentable);

    /**
     * Gets the number of comments
     *
     * @return integer
     */
    public function getNumComments();

    /**
     * Sets the number of comments
     *
     * @param integer $numComments
     */
    public function setNumComments($numComments);

    /**
     * Increments the number of comments by the supplied
     * value.
     *
     * @param  integer $by The number of comments to increment by
     * @return integer The new comment total
     */
    public function incrementNumComments($by);

    /**
     * Denormalized date of the last comment
     * @return DateTime
     */
    public function getLastCommentAt();

    /**
     * @param  DateTime
     * @return null
     */
     function setLastCommentAt($lastCommentAt);
}
