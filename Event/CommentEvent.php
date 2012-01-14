<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Event;

use FOS\CommentBundle\Model\CommentInterface;
use Symfony\Component\EventDispatcher\Event;

class CommentEvent extends Event
{
    private $comment;

    /**
     * Constructs an event.
     *
     * @param CommentInterface $comment
     */
    public function __construct(CommentInterface $comment)
    {
        $this->comment = $comment;
    }

    public function getComment()
    {
        return $this->comment;
    }
}