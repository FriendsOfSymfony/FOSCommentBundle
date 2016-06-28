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

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\ExecutionContextInterface as LegacyExecutionContextInterface;

/**
 * Methods a flag should implement.
 *
 * @author Hubert Bryłkowski <hubert@brylkowski.com>
 */
interface FlagInterface
{

    /**
     * @return mixed unique ID for this flag
     */
    public function getId();

    /**
     * @return SignedCommentInterface
     */
    public function getComment();

    /**
     * @param FlaggableCommentInterface $comment
     */
    public function setComment(FlaggableCommentInterface $comment);

    /**
     * @return integer the flag type
     */
    public function getType();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @return string
     */
    public function getReason();

    /**
     * @param string $body
     */
    public function setReason($body);

}
