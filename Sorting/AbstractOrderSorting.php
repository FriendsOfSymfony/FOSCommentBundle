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
            throw new InvalidArgumentException(sprintf("%s is an invalid sorting order", $order));
        }
    }

    /**
     * Sorts an array of Tree elements.
     *
     * The array should be in the format of:
     *
     *    array(
     *        'comment' => CommentInterface $comment,
     *        'children' => array ( .. )
     *    )
     *
     * @param  array $tree
     * @return array
     */
    public function sort(array $tree)
    {
        foreach ($tree as &$branch) {
            if (count($branch['children'])) {
                $branch['children'] = $this->sort($branch['children']);
            }
        }

        usort($tree, array($this, 'doSort'));

        return $tree;
    }

    /**
     * Compares two arrays from the Comment Tree.
     *
     * @param  array  $a
     * @param  array  $b
     * @return -1|0|1 As expected for usort()
     */
    public function doSort($a, $b)
    {
        if ($this->order == self::ASC) {
            return $this->compare($a['comment'], $b['comment']);
        } else {
            return $this->compare($b['comment'], $a['comment']);
        }
    }

    /**
     * Sorts a flat array of comments.
     *
     * @param  array $comments
     * @return array
     */
    public function sortFlat(array $comments)
    {
        usort($comments, array($this, 'doFlatSort'));

        return $comments;
    }

    /**
     * Compares two comments from a flat array.
     *
     * @param  CommentInterface $a
     * @param  CommentInterface $b
     * @return -1|0|1           As expected for uasort()
     */
    public function doFlatSort($a, $b)
    {
        if ($this->order == self::ASC) {
            return $this->compare($a, $b);
        } else {
            return $this->compare($b, $a);
        }
    }

    /**
     * Compares 2 comments. Implement this to create custom sorting options.
     *
     * @param  CommentInterface $a
     * @param  CommentInterface $b
     * @return -1|0|1           As expected for usort()
     */
    abstract protected function compare(CommentInterface $a, CommentInterface $b);
}
