<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
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
        foreach ($container->findTaggedServiceIds('fos_comment.sorter') as $id => $attributes) {
            if (!isset($attributes[0]['alias'])) {
                throw new InvalidArgumentException('The AI must have an alias');
            }

            $sorters[$attributes[0]['alias']] = new Reference($id);
        }

        $container->getDefinition('fos_comment.sorting_factory')->setArgument(0, $sorters);
    }
}