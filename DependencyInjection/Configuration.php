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
            ->children()

                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->end()

                ->arrayNode('class')->isRequired()
                    ->children()
                        ->arrayNode('model')->isRequired()
                            ->children()
                                ->scalarNode('comment')->isRequired()->end()
                            ->end()
                        ->end()
                        ->arrayNode('form')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('FOS\CommentBundle\Form\CommentForm')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('service')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('form_factory')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.form_factory.comment.default')->end()
                            ->end()
                        ->end()
                        ->arrayNode('creator')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('thread')->cannotBeEmpty()->defaultValue('fos_comment.creator.thread.default')->end()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.creator.comment.default')->end()
                            ->end()
                        ->end()
                        ->arrayNode('blamer')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.blamer.comment.noop')->end()
                            ->end()
                        ->end()
                        ->arrayNode('spam_detection')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.spam_detection.comment.noop')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('akismet')->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('url')->defaultValue(null)->end()
                        ->scalarNode('api_key')->defaultValue(null)->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder->buildTree();
    }
}
