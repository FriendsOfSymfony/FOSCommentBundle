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
 * A comment that may be flagged.
 *
 * @author Hubert Brylkowski <hubert@brylkowski.com>
 */
interface FlaggableCommentInterface extends CommentInterface
{
    const FLAG_INAPPROPRIATE = 0;

    const FLAG_SPAM = 1;

    const FLAG_ABUSIVE = 2;

    /**
     * @param $flag FlagInterface
     */
    public function addFlag($flag);

    /**
     * @return FlagInterface[]
     */
    public function getFlags();
}
