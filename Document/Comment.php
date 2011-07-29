<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Document;

use FOS\CommentBundle\Model\Comment as AbstractComment;

/**
 * Default ODM implementation of CommentInterface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class Comment extends AbstractComment
{
    /**
     * All ancestors of the comment
     *
     * @var array
     */
    protected $ancestors = array();

    /**
     * @return array
     */
    public function getAncestors()
    {
        return $this->ancestors;
    }

    /**
     * @param  array
     * @return null
     */
    public function setAncestors(array $ancestors)
    {
        $this->ancestors = $ancestors;
        $this->depth = count($ancestors);
    }
}
