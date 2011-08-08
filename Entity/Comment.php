<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Entity;

use FOS\CommentBundle\Model\Comment as AbstractComment;

/**
 * Default ORM implementation of CommentInterface.
 *
 * Must be extended and properly mapped by the end developer.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
abstract class Comment extends AbstractComment
{
    /**
     * All ancestors of the comment
     *
     * @var string
     */
    protected $ancestors = '';

    /**
     * @return array
     */
    public function getAncestors()
    {
        return $this->ancestors ? explode('/', $this->ancestors) : array();
    }

    /**
     * @param  array
     * @return null
     */
    public function setAncestors(array $ancestors)
    {
        $this->ancestors = implode('/', $ancestors);
        $this->depth = count($ancestors);
    }
}
