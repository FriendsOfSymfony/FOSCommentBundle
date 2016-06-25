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
 * Manages flagging for comments.
 *
 * @author Hubert Bryłkowski <hubert@brylkowski.com>
 */
interface FlagManagerInterface
{
    /**
     * Returns the class of the Flag object.
     *
     * @return string
     */
    public function getClass();

    /**
     * Creates a Flag object.
     *
     * @param  FlaggableCommentInterface $comment
     * @return FlagInterface
     */
    public function createFlag(FlaggableCommentInterface $comment);

    /**
     * Persists a flag.
     *
     * @param  FlagInterface $flag
     * @return void
     */
    public function saveFlag(FlagInterface $flag);

    /**
     * Finds a flag by specified criteria.
     *
     * @param  array         $criteria
     * @return FlagInterface
     */
    public function findFlagBy(array $criteria);

    /**
     * Finds a flag by id.
     *
     * @param  $id
     * @return FlagInterface
     */
    public function findFlagById($id);

    /**
     * Finds all flags for a comment.
     *
     * @param  FlaggableCommentInterface $comment
     * @return FlagInterface[]
     */
    public function findFlagsByComment(FlaggableCommentInterface $comment);
}
