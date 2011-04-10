<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Sorting;

use FOS\CommentBundle\Model\CommentInterface;
use InvalidArgumentException;

/**
 * Sorts comments by vote order.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class AgedVoteSorting extends AbstractOrderSorting
{
    protected function compare(CommentInterface $a, CommentInterface $b)
    {
        $aScore = $a->getScore() * ($a->getCreatedAt()->getTimestamp() / 60 / 60 / 24);
        $bScore = $b->getScore() * ($b->getCreatedAt()->getTimestamp() / 60 / 60 / 24);

        if ($aScore == $bScore) {
            return 0;
        }

        return $aScore < $bScore ? -1 : 1;
    }
}