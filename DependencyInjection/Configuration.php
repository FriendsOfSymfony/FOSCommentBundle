<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
     * @return NodeInterface
     */
    public function getConfigTree()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('fos_comment', 'array')
            ->children()

                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()

                ->arrayNode('form')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('comment')->isRequired()->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_comment.comment')->end()
                                ->scalarNode('name')->defaultValue('fos_comment_comment')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('class')->isRequired()
                    ->children()
                        ->arrayNode('model')->isRequired()
                            ->children()
                                ->scalarNode('comment')->isRequired()->end()
                                ->scalarNode('thread')->isRequired()->end()
                                ->scalarNode('vote')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('acl')->end()

                ->arrayNode('acl_roles')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('comment')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                        ->arrayNode('thread')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                        ->arrayNode('vote')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('create')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('view')->cannotBeEmpty()->defaultValue('IS_AUTHENTICATED_ANONYMOUSLY')->end()
                                ->scalarNode('edit')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                                ->scalarNode('delete')->cannotBeEmpty()->defaultValue('ROLE_ADMIN')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('template')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('engine')->defaultValue('twig')->end()
                    ->end()
                ->end()

                ->arrayNode('service')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('manager')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.manager.comment.default')->end()
                                ->scalarNode('thread')->cannotBeEmpty()->defaultValue('fos_comment.manager.thread.default')->end()
                                ->scalarNode('vote')->cannotBeEmpty()->defaultValue('fos_comment.manager.vote.default')->end()
                            ->end()
                        ->end()
                        ->arrayNode('acl')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.acl.comment.security')->end()
                                ->scalarNode('thread')->cannotBeEmpty()->defaultValue('fos_comment.acl.thread.security')->end()
                                ->scalarNode('vote')->cannotBeEmpty()->defaultValue('fos_comment.acl.vote.security')->end()
                            ->end()
                        ->end()
                        ->arrayNode('form_factory')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.form_factory.comment.default')->end()
                            ->end()
                        ->end()
                        ->arrayNode('creator')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.creator.comment.default')->end()
                                ->scalarNode('thread')->cannotBeEmpty()->defaultValue('fos_comment.creator.thread.default')->end()
                                ->scalarNode('vote')->cannotBeEmpty()->defaultValue('fos_comment.creator.vote.default')->end()
                            ->end()
                        ->end()
                        ->arrayNode('blamer')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.blamer.comment.noop')->end()
                                ->scalarNode('vote')->cannotBeEmpty()->defaultValue('fos_comment.blamer.vote.noop')->end()
                            ->end()
                        ->end()
                        ->arrayNode('spam_detection')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('comment')->cannotBeEmpty()->defaultValue('fos_comment.spam_detection.comment.noop')->end()
                            ->end()
                        ->end()
                        ->arrayNode('sorting')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default')->cannotBeEmpty()->defaultValue('date_desc')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder->buildTree();
    }
}
