<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Document;

use FOS\CommentBundle\Model\Comment as AbstractComment;

class Comment extends AbstractComment
{
    /**
     * Thread of this comment
     *
     * @var CommentThread
     */
    protected $thread;

    /**
     * @return CommentThread
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * @param  CommentThread
     * @return null
     */
    public function setThread($thread)
    {
        $this->thread = $thread;
    }
}
