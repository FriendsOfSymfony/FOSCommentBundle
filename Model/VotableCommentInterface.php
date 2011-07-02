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

/**
 * A comment that may be voted on.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface VotableCommentInterface extends CommentInterface
{
    /**
     * Sets the score of the comment.
     *
     * @param integer $score
     */
    function setScore($score);

    /**
     * Returns the current score of the comment.
     *
     * @return integer
     */
    function getScore();

    /**
     * Increments the comment score by the provided
     * value.
     *
     * @param integer value
     * @return integer The new comment score
     */
    function incrementScore($by = 1);
}
