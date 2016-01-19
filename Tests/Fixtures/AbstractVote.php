<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Fixtures;

use FOS\CommentBundle\Model\VoteInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\ExecutionContextInterface as LegacyExecutionContextInterface;

abstract class AbstractVote implements VoteInterface
{
    /**
     * {@inheritdoc}
     */
    public function isVoteValid(LegacyExecutionContextInterface $context) {
    }

    /**
     * {@inheritdoc}
     */
    public function isValid(ExecutionContextInterface $context) {
    }
}
