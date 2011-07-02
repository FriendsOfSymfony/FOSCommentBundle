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
use InvalidArgumentException;

/**
 * Storage agnostic vote object - Requires FOS\UserBundle
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
abstract class Vote implements VoteInterface
{
    /**
     * @var mixed
     */
    protected $id;

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
     * @var DateTime
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * The value of the vote.
     *
     * @var integer
     */
    protected $value;

    /**
     * @return integer The votes value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param integer $value
     */
    public function setValue($value)
    {
        $value = intval($value);

        if (!$value) {
            throw new InvalidArgumentException('A vote cannot have a 0 value');
        }

        $this->value = intval($value);
    }


    public function __toString()
    {
        return 'Vote #'.$this->getId();
    }
}
