<?php

namespace FOS\CommentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 */
class Configuration
{
    /**
     * Generates the configuration tree.
     *
     * @return \Symfony\Component\DependencyInjection\Configuration\NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('fos_comment', 'array');

        $rootNode
            ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->cannotBeEmpty()->end();

        $this->addClassSection($rootNode);

        return $treeBuilder->buildTree();
    }

    private function addClassSection(NodeBuilder $node)
    {
        $node
            ->arrayNode('class')
                ->isRequired()
                ->addDefaultsIfNotSet()
                ->arrayNode('model')
                    ->isRequired()
                    ->scalarNode('comment')->isRequired()->cannotBeEmpty()->end()
                ->end()
            ->end();
    }
}
