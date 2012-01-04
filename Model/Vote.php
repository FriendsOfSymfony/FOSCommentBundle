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

use Symfony\Component\Validator\ExecutionContext;

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
        if (!$this->checkValue($value)) {
            throw new InvalidArgumentException('A vote cannot have a 0 value');
        }

        $this->value = intval($value);
    }

    /**
     * {@inheritdoc}
     */
    public function isVoteValid(ExecutionContext $context)
    {
        if (!$this->checkValue($this->value)) {
            $propertyPath = $context->getPropertyPath() . '.value';
            $context->setPropertyPath($propertyPath);
            $context->addViolation('A vote cannot have a 0 value', array(), null);
        }
    }

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
}
