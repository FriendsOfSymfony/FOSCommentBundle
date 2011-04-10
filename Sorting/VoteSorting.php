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
class VoteSorting extends AbstractOrderSorting
{
    protected function compare(CommentInterface $a, CommentInterface $b)
    {
        if ($a->getScore() == $b->getScore()) {
            return 0;
        }

        return $a->getScore() < $b->getScore() ? -1 : 1;
    }
}