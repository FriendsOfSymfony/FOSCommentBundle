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
     * @var DateTime
     */
    protected $createdAt;

    /**
     * @var DateTime
     */
    protected $updatedAt;

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
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function incrementCreatedAt()
    {
        if (null === $this->createdAt) {
            $this->createdAt = new DateTime();
        }
        $this->updatedAt = new DateTime();
    }

    public function incrementUpdatedAt()
    {
        $this->updatedAt = new DateTime();
    }

    public function __toString()
    {
        return 'Comment #'.$this->getId();
    }
}
