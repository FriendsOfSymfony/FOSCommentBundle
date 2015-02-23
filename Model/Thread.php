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
 * Storage agnostic comment thread object
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Thread implements ThreadInterface
{
    /**
     * Id, a unique string that binds the comments together in a thread (tree).
     * It can be a url or really anything unique.
     *
     * @var string
     */
    protected $id;

    /**
     * Tells if new comments can be added in this thread
     *
     * @var bool
     */
    protected $isCommentable = true;

    /**
     * Denormalized number of comments
     *
     * @var integer
     */
    protected $numComments = 0;

    /**
     * Denormalized date of the last comment
     *
     * @var DateTime
     */
    protected $lastCommentAt = null;

    /**
     * Url of the page where the thread lives
     *
     * @var string
     */
    protected $permalink;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param  string
     * @return null
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getPermalink()
    {
        return $this->permalink;
    }

    /**
     * @param  string
     * @return null
     */
    public function setPermalink($permalink)
    {
        $this->permalink = $permalink;
    }

    /**
     * @return bool
     */
    public function isCommentable()
    {
        return $this->isCommentable;
    }

    /**
     * @param  bool
     * @return null
     */
    public function setCommentable($isCommentable)
    {
        $this->isCommentable = (bool) $isCommentable;
    }

    /**
     * Gets the number of comments
     *
     * @return integer
     */
    public function getNumComments()
    {
        return $this->numComments;
    }

    /**
     * Sets the number of comments
     *
     * @param integer $numComments
     */
    public function setNumComments($numComments)
    {
        $this->numComments = intval($numComments);
    }

    /**
     * Increments the number of comments by the supplied
     * value.
     *
     * @param  integer $by Value to increment comments by
     * @return integer The new comment total
     */
    public function incrementNumComments($by = 1)
    {
        return $this->numComments += intval($by);
    }

    /**
     * @return DateTime
     */
    public function getLastCommentAt()
    {
        return $this->lastCommentAt;
    }

    /**
     * @param  DateTime
     * @return null
     */
    public function setLastCommentAt($lastCommentAt)
    {
        $this->lastCommentAt = $lastCommentAt;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'Comment thread #'.$this->getId();
    }
}
