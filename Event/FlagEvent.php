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

use FOS\CommentBundle\Model\FlagInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * An event that occurs related to a flag.
 *
 * @author Hubert Bryłkowski <hubert@brylkowski.com>
 */
class FlagEvent extends Event
{
    private $flag;

    /**
     * Constructs an event.
     *
     * @param FlagInterface $flag
     */
    public function __construct(FlagInterface $flag)
    {
        $this->flag = $flag;
    }

    /**
     * Returns the flag for the event.
     *
     * @return \FOS\CommentBundle\Model\FlagInterface
     */
    public function getFlag()
    {
        return $this->flag;
    }
}
