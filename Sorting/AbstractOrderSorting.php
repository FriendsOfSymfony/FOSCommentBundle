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
 * Sorts comments by date order.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
abstract class AbstractOrderSorting implements SortingInterface
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

        uasort($tree, array($this, 'doSort'));

        return $tree;
    }

    public function doSort($a, $b)
    {
        if ($this->order == self::ASC) {
            return $this->compare($a['comment'], $b['comment']);
        } else {
            return $this->compare($b['comment'], $a['comment']);
        }
    }

    abstract protected function compare(CommentInterface $a, CommentInterface $b);
}