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

use FOS\CommentBundle\Model\Tree;

/**
 * Interface to be implemented for adding additional Sorting strategies.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface SortingInterface
{
    const POSITION_TOP = 'top';
    const POSITION_BOTTOM = 'bottom';

    /**
     * Takes an array of Tree instances and sorts them.
     *
     * @param  array $tree
     * @return Tree
     */
    public function sort(array $tree);

    /**
     * Sorts a flat comment array.
     *
     * @param  array $comments
     * @return array
     */
    public function sortFlat(array $comments);

    /**
     * Returns the position constant to be used to position new
     * comments made to a thread.
     *
     * @return string
     */
    public function getNewPosition();
}
