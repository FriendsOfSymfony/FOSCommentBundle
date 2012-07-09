<?php

/**
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
 * Sorts comments by vote order.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class VoteSorting extends AbstractOrderSorting
{
    /**
     * Compares the comments score.
     *
     * @param  CommentInterface $a
     * @param  CommentInterface $b
     * @return -1|0|1           As expected for uasort()
     */
    protected function compare(CommentInterface $a, CommentInterface $b)
    {
        if ($a->getScore() == $b->getScore()) {
            return 0;
        }

        return $a->getScore() < $b->getScore() ? -1 : 1;
    }
}
