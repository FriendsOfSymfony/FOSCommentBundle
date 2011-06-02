<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use InvalidArgumentException;

/**
 * Registers Sorting implementations.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SortingPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fos_comment.sorting_factory')) {
            return;
        }

        $sorters = array();
        foreach ($container->findTaggedServiceIds('fos_comment.sorter') as $id => $tags) {
            foreach ($tags as $tag) {
                if (empty($tag['alias'])) {
                    throw new InvalidArgumentException('The Sorter must have an alias');
                }

                $sorters[$tag['alias']] = new Reference($id);
            }
        }

        $container->getDefinition('fos_comment.sorting_factory')->replaceArgument(0, $sorters);
    }
}
