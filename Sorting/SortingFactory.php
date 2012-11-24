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

use InvalidArgumentException;
use RuntimeException;

/**
 * Sorting Factory.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SortingFactory
{
    /**
     * @var array of SortingInterface
     */
    private $sorters;

    /**
     * @var string Default SortingInterface alias
     */
    private $defaultSorter;

    /**
     * @param array  $sorters       An array of SortingInterfaces
     * @param string $defaultSorter The alias of the sorter to use by default
     */
    public function __construct(array $sorters, $defaultSorter)
    {
        foreach ($sorters as $alias => $sorter) {
            if (!$sorter instanceof SortingInterface) {
                throw new InvalidArgumentException('Sorters must implement SortingInterface');
            }

            $this->sorters[$alias] = $sorter;
        }

        $this->defaultSorter = $defaultSorter;
    }

    public function getSorter($alias = null)
    {
        if (empty($alias)) {
            $alias = $this->defaultSorter;
        }

        if (!array_key_exists($alias, $this->sorters)) {
            throw new RuntimeException(sprintf("Unknown sorting alias '%s'", $alias));
        }

        return $this->sorters[$alias];
    }

    public function getAvailableSorters()
    {
        return array_keys($this->sorters);
    }
}
