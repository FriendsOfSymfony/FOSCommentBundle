<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

/**
 * Storage agnostic comment thread object
 */
abstract class Thread implements ThreadInterface
{
    /**
     * Identifier, a unique string that binds the comments together in a thread (tree).
     * It can be a url or really anything unique.
     *
     * @var string
     */
    protected $identifier;

    /**
     * Tells if new comments can be added in this thread
     *
     * @var bool
     */
    protected $isCommentable = true;

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param  string
     * @return null
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return bool
     */
    public function getIsCommentable()
    {
        return $this->isCommentable;
    }

    /**
     * @param  bool
     * @return null
     */
    public function setIsCommentable($isCommentable)
    {
        $this->isCommentable = (bool) $isCommentable;
    }

    public function __toString()
    {
        return 'Comment thread #'.$this->getIdentifier();
    }
}
