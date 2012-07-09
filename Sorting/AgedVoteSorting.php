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
 * Sorts comments by aged vote order.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class AgedVoteSorting extends AbstractOrderSorting
{
    /**
     * Compares the comments score divided by the number of days since the 1970.
     *
     * The end result is a comment that is newer with tje same votes will be ranked
     * higher.
     *
     * @param  CommentInterface $a
     * @param  CommentInterface $b
     * @return -1|0|1           As expected for uasort()
     */
    protected function compare(CommentInterface $a, CommentInterface $b)
    {
        $aScore = $a->getScore() / ($a->getCreatedAt()->getTimestamp() / 60 / 60 / 24);
        $bScore = $b->getScore() / ($b->getCreatedAt()->getTimestamp() / 60 / 60 / 24);

        if ($aScore == $bScore) {
            return 0;
        }

        return $aScore < $bScore ? -1 : 1;
    }
}
