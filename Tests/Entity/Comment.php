<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Entity;

use FOS\CommentBundle\Entity\Comment as BaseComment,
    FOS\CommentBundle\Model\ThreadInterface;

class Comment extends BaseComment
{
    /**
     * Thread of this comment
     *
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * @return ThreadInterface
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param  Thread $thread
     * @return null
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
    }
}
