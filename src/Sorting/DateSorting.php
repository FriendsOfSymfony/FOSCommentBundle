<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Sorting;

use FOS\CommentBundle\Model\CommentInterface;

/**
 * Sorts comments by date order.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class DateSorting extends AbstractOrderSorting
{
    /**
     * Compares the comments creation date.
     *
     * @return -1|0|1 As expected for uasort()
     */
    protected function compare(CommentInterface $a, CommentInterface $b)
    {
        if ($a->getCreatedAt() == $b->getCreatedAt()) {
            return 0;
        }

        return $a->getCreatedAt() < $b->getCreatedAt() ? -1 : 1;
    }
}
