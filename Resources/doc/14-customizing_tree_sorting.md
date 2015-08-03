CommentBundle also provides the ability to customize the sorting of the comment tree. 
See the configuration example below for how to customise the default sorting, which is descending by date.

To configure ascending sorting by date:

``` yaml
# app/config/config.yml
fos_comment:
    service:
        sorting:
            default: date_asc
```


If you wish to sort comment threads in a custom way which is not provided by FOSCommentBundle you may
do so by creating a custom sorting service by implementing the SortingInterface and declaring it as a service.

For example:

``` php
<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Acme\CommentBundle\Sorting;

use FOS\CommentBundle\Sorting\SortingInterface;

/**
 *
 */
class AcmeOrderSorting implements SortingInterface
{
   /**
     * Takes an array of Tree instances and sorts them.
     *
     * @param  array $tree
     * @return Tree
     */
    public function sort(array $tree)
    {
        //Implement sorting strategy
    }

    /**
     * Sorts a flat comment array.
     *
     * @param  array $comments
     * @return array
     */
    public function sortFlat(array $comments)
    {
        //Implement sorting strategy
    }
}
```

Then declare the sorter as a service and configure CommentBundle to use it


``` yaml
services:
    acme_comment.sorter.my_sort:
        class: Acme\CommentBundle\Sorting\AcmeOrderSorting
        tags:
            - { name: fos_comment.sorter, alias: my_sort }
```
