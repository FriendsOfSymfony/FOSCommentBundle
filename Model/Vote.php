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
     * @var VotableCommentInterface
     */
    protected $comment;

    /**
     * @var DateTime
     */
    protected $createdAt;

    /**
     * The value of the vote.
     *
     * @var integer
     */
    protected $value;

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
        $this->value = intval($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isVoteValid($context)
    {
        if($context instanceof ExecutionContextInterface) {
            if (!$this->checkValue($this->value)) {
                $message = 'A vote cannot have a 0 value';
                $propertyPath = $context->getPropertyPath() . '.value';

                // Validator 2.5 API
                $context->buildViolation($message)
                    ->atPath($propertyPath)
                    ->addViolation();
            }
        } elseif (!$this->checkValue($this->value)) { // For bc
            $message = 'A vote cannot have a 0 value';
            $propertyPath = $context->getPropertyPath() . '.value';

            $context->addViolationAt($propertyPath, $message);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'Vote #'.$this->getId();
    }

    /**
     * Checks if the value is an appropriate one.
     *
     * @param mixed $value
     *
     * @return boolean True, if the integer representation of the value is not null or 0.
     */
    protected function checkValue($value)
    {
        return null !== $value && intval($value);
    }

    /**
     * Gets the comment this vote belongs to.
     *
     * @return VotableCommentInterface
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets the comment this vote belongs to.
     *
     * @param  VotableCommentInterface $comment
     * @return void
     */
    public function setComment(VotableCommentInterface $comment)
    {
        $this->comment = $comment;
    }
}
