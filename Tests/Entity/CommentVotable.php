<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Entity;

use FOS\CommentBundle\Model\VotableCommentInterface;

class CommentVotable extends Comment implements VotableCommentInterface
{
    /**
     * @var int
     */
    protected $score = 0;

    /**
     * @param int $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param int $by
     */
    public function incrementScore($by = 1)
    {
        $this->score += $by;
    }

    /**
     * @param int $by
     */
    public function decrementScore($by = 1)
    {
        $this->score -= $by;
    }
}
