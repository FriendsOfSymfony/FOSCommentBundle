<?php

/**
 * This file is part of the FOS\CommentBundle.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
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
    /**
     * Takes an array of Tree instances and sorts them.
     *
     * @param array $tree
     * @return Tree
     */
    function sort(array $tree);
}
