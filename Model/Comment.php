<?php

/**
 * This file is part of the FOS\CommentBundle.
 *
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

use DateTime;

/**
 * Storage agnostic comment object
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Comment implements CommentInterface
{
    /**
     * Comment id
     *
     * @var mixed
     */
    protected $id;

    /**
     * Comment text
     *
     * @var string
     */
    protected $body;

    /**
     * The depth of the comment
     *
     * @var integer
     */
    protected $depth = 0;

    /**
     * @var DateTime
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Return the comment unique id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param  string
     * @return null
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return string name of the comment author
     */
    public function getAuthorName()
    {
        return 'Anonymous';
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function __toString()
    {
        return 'Comment #'.$this->getId();
    }

    /**
     * Returns the depth of the comment.
     *
     * @return integer
     */
    public function getDepth()
    {
        return $this->depth;
    }
}
