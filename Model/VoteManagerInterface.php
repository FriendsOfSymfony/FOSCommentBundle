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
 * Manages voting scores for comments.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface VoteManagerInterface
{
    /**
     * Returns the class of the Vote object.
     *
     * @return string
     */
    function getClass();

    /**
     * Creates a Vote object.
     *
     * @param VotableCommentInterface $comment
     * @return VoteInterface
     */
    function createVote(VotableCommentInterface $comment);

    /**
     * Persists a vote.
     *
     * @param VoteInterface $vote
     * @return void
     */
    function saveVote(VoteInterface $vote);

    /**
     * Finds a vote by specified criteria.
     *
     * @param array $criteria
     * @return VoteInterface
     */
    function findVoteBy(array $criteria);

    /**
     * Finds a vote by id.
     *
     * @param  $id
     * @return VoteInterface
     */
    function findVoteById($id);

    /**
     * Finds all votes for a comment.
     *
     * @param VotableCommentInterface $comment
     * @return array of VoteInterface
     */
    function findVotesByComment(VotableCommentInterface $comment);
}
