<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

use DateTime;

/**
 * Storage agnostic comment object
 */
abstract class Comment implements CommentInterface
{
    protected $id;

    /**
     * Comment text
     *
     * @var string
     */
    protected $body;

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
}
