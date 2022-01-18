<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Event;

use Symfony\Component\EventDispatcher\Event as BaseEventDeprecated;
use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

// Symfony 4.3 BC layer
if (class_exists(BaseEvent::class)) {
    class Event extends BaseEvent
    {
    }
} else {
    class Event extends BaseEventDeprecated
    {
    }
}
