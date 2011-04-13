<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Sorting;

use FOS\CommentBundle\Model\Tree;
use InvalidArgumentException;

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
            throw InvalidArgumentException(sprintf("%s is an invalid sorting order", $order));
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
        $ascending = ($this->order == self::ASC);

        foreach ($tree AS &$branch) {
            if (count($branch['children'])) {
                $branch['children'] = $this->sort($branch['children']);
            }
        }

        uasort($tree, function ($a, $b) use ($ascending) {
            if ($a['comment']->getCreatedAt() < $b['comment']->getCreatedAt()) {
                return 0;
            }

            if ($ascending) {
                return ($a['comment']->getCreatedAt() < $b['comment']->getCreatedAt()) ? -1 : 1;
            } else {
                return ($a['comment']->getCreatedAt() < $b['comment']->getCreatedAt()) ? 1 : -1;
            }
        });

        return $tree;
    }
}