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
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\ExecutionContextInterface as LegacyExecutionContextInterface;

/**
 * Storage agnostic flag object
 *
 * @author Hubert Bryłkowski <hubert@brylkowski.com>
 */
abstract class Flag implements FlagInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var FlaggableCommentInterface
     */
    protected $comment;

    /**
     * @var DateTime
     */
    protected $createdAt;

    /**
     * The flag type
     *
     * @var integer
     */
    protected $type;

    /**
     * @param VotableCommentInterface $comment
     */
    public function __construct(VotableCommentInterface $comment = null)
    {
        $this->comment = $comment;
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
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return integer The flag type.
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param integer $type
     */
    public function setType($type)
    {
        $this->type = intval($type);
    }

    /**
     * Gets the comment this flag belongs to.
     *
     * @return VotableCommentInterface
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the comment this flag belongs to.
     *
     * @param  FlaggableCommentInterface $comment
     * @return void
     */
    public function setComment(FlaggableCommentInterface $comment)
    {
        $this->comment = $comment;
    }
}
