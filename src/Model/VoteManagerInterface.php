<?php

/*
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
    public function getClass();

    /**
     * Creates a Vote object.
     *
     * @return VoteInterface
     */
    public function createVote(VotableCommentInterface $comment);

    /**
     * Persists a vote.
     *
     * @return void
     */
    public function saveVote(VoteInterface $vote);

    /**
     * Finds a vote by specified criteria.
     *
     * @return VoteInterface
     */
    public function findVoteBy(array $criteria);

    /**
     * Finds a vote by id.
     *
     * @param $id
     *
     * @return VoteInterface
     */
    public function findVoteById($id);

    /**
     * Finds all votes for a comment.
     *
     * @return array of VoteInterface
     */
    public function findVotesByComment(VotableCommentInterface $comment);
}
