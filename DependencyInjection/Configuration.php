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

        $treeBuilder->root('fos_comment', 'array')

            ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->end()

            ->arrayNode('akismet')->addDefaultsIfNotSet()
                ->booleanNode('enabled')->defaultFalse()->end()
                ->scalarNode('api_key')->end()
                ->scalarNode('url')->end()
            ->end()

            ->arrayNode('class')->isRequired()
                ->arrayNode('model')->isRequired()
                    ->scalarNode('comment')->isRequired()->end()
                ->end()
                ->arrayNode('form')->addDefaultsIfNotSet()
                    ->scalarNode('comment')->cannotBeEmpty()->defaultValue('FOS\CommentBundle\Form\CommentForm')->end()
                ->end()
            ->end()

            ->arrayNode('service')->addDefaultsIfNotSet()
                ->arrayNode('form_factory')->addDefaultsIfNotSet()
                    ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.form_factory.comment.default')->end()
                ->end()
                ->arrayNode('creator')->addDefaultsIfNotSet()
                    ->scalarNode('thread')->cannotBeEmpty()->defaultValue('fos_comment.creator.thread.default')->end()
                    ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.creator.comment.default')->end()
                ->end()
                ->arrayNode('blamer')->addDefaultsIfNotSet()
                    ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.blamer.comment.noop')->end()
                ->end()
            ->end();

        return $treeBuilder->buildTree();
    }
}
