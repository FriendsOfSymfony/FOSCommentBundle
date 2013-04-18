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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This class contains the configuration information for the bundle
 *
 * This information is solely responsible for how the different configuration
 * sections are normalized, and merged.
 */
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return NodeInterface
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder->root('fos_comment')
            ->children()

                ->scalarNode('db_driver')->cannotBeOverwritten()->isRequired()->end()
                ->scalarNode('model_manager_name')->defaultNull()->end()

                ->arrayNode('form')->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('comment')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_comment_comment')->end()
                                ->scalarNode('name')->defaultValue('fos_comment_comment')->end()
                            ->end()
                        ->end()
                        ->arrayNode('thread')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_comment_thread')->end()
                                ->scalarNode('name')->defaultValue('fos_comment_thread')->end()
                            ->end()
                        ->end()
                        ->arrayNode('commentable_thread')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_comment_commentable_thread')->end()
                                ->scalarNode('name')->defaultValue('fos_comment_commentable_thread')->end()
                            ->end()
                        ->end()
                        ->arrayNode('delete_comment')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_comment_delete_comment')->end()
                                ->scalarNode('name')->defaultValue('fos_comment_delete_comment')->end()
                            ->end()
                        ->end()
                        ->arrayNode('vote')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('type')->defaultValue('fos_comment_vote')->end()
                                ->scalarNode('name')->defaultValue('fos_comment_vote')->end()
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
                                ->scalarNode('commentable_thread')->cannotBeEmpty()->defaultValue('fos_comment.form_factory.commentable_thread.default')->end()
                                ->scalarNode('delete_comment')->cannotBeEmpty()->defaultValue('fos_comment.form_factory.delete_comment.default')->end()
                                ->scalarNode('thread')->cannotBeEmpty()->defaultValue('fos_comment.form_factory.thread.default')->end()
                                ->scalarNode('vote')->cannotBeEmpty()->defaultValue('fos_comment.form_factory.vote.default')->end()
                            ->end()
                        ->end()
                        ->arrayNode('spam_detection')
                            ->children()
                                ->scalarNode('comment')->end()
                            ->end()
                        ->end()
                        ->arrayNode('sorting')->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('default')->cannotBeEmpty()->defaultValue('date_desc')->end()
                            ->end()
                        ->end()
                        ->scalarNode('markup')->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
