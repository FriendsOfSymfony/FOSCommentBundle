<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Sorting;

use FOS\CommentBundle\Model\Tree;

/**
 * Sorts comments by date order.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class DateSorting implements SortingInterface
{
    const ASC = 'ASC';
    const DESC = 'DESC';

    private $order;

    public function __construct($order)
    {
        if ($order == self::ASC || $order == self::DESC) {
            $this->order = $order;
        } else {
            $this->order = self::DESC;
        }
    }

    /**
     * Sorts an array of Tree elements.
     *
     * @param array $tree
     * @return array
     */
    public function sort(array $tree)
    {
        $ascending = $this->order == self::ASC;

        uasort($tree, function ($a, $b) use ($ascending) {
            $a = $a->getComment();
            $b = $b->getComment();

            if (!$a || !$b) {
                // We dont have a comment object in one of the comparisons
                // Not sure if this condition will actually ever occur, but
                // is this the right way of dealing with it?
                return 0;
            }

            if ($a->getCreatedAt() == $b->getCreatedAt()) {
                return 0;
            }

            if ($ascending) {
                return ($a->getCreatedAt() < $b->getCreatedAt()) ? -1 : 1;
            } else {
                return ($a->getCreatedAt() < $b->getCreatedAt()) ? 1 : -1;
            }
        });

        return $tree;
    }
}