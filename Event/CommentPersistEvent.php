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

/**
 * An event related to a persisting event that can be
 * cancelled by a listener.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentPersistEvent extends CommentEvent
{
    /**
     * @var bool
     */
    private $abortPersist = false;

    /**
     * Indicates that the persisting operation should not proceed.
     */
    public function abortPersist()
    {
        $this->abortPersist = true;
    }

    /**
     * Checks if a listener has set the event to abort the persisting
     * operation.
     *
     * @return bool
     */
    public function isAbortPersist()
    {
        return $this->abortPersist;
    }
}