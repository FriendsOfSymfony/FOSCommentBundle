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

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Configures the DI container for CommentBundle.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class FOSCommentExtension extends Extension
{
    /**
     * Loads and processes configuration to configure the Container.
     *
     * @throws InvalidArgumentException
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->process($configuration->getConfigTree(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (!in_array(strtolower($config['db_driver']), array('mongodb', 'orm'))) {
            throw new \InvalidArgumentException(sprintf('Invalid db driver "%s".', $config['db_driver']));
        }
        $loader->load(sprintf('%s.xml', $config['db_driver']));

        foreach (array('blamer', 'form', 'creator', 'spam_detection', 'twig', 'sorting') as $basename) {
            $loader->load(sprintf('%s.xml', $basename));
        }

        // only load acl services if acl is enabled for the project
        if (array_key_exists('acl', $config)) {
            $this->loadAcl($container, $config);
        }

        $container->setParameter('fos_comment.model.comment.class', $config['class']['model']['comment']);
        $container->setParameter('fos_comment.model.thread.class', $config['class']['model']['thread']);

        if (array_key_exists('vote', $config['class']['model'])) {
            $container->setParameter('fos_comment.model.vote.class', $config['class']['model']['vote']);
        }

        $container->setParameter('fos_comment.model_manager_name', $config['model_manager_name']);

        $container->setParameter('fos_comment.form.comment.type', $config['form']['comment']['type']);
        $container->setParameter('fos_comment.form.comment.name', $config['form']['comment']['name']);

        $container->setParameter('fos_comment.sorting_factory.default_sorter', $config['service']['sorting']['default']);

        $container->setAlias('fos_comment.form_factory.comment', $config['service']['form_factory']['comment']);
        $container->setAlias('fos_comment.creator.thread', $config['service']['creator']['thread']);
        $container->setAlias('fos_comment.creator.comment', $config['service']['creator']['comment']);
        $container->setAlias('fos_comment.creator.vote', $config['service']['creator']['vote']);
        $container->setAlias('fos_comment.blamer.comment', $config['service']['blamer']['comment']);
        $container->setAlias('fos_comment.blamer.vote', $config['service']['blamer']['vote']);
        $container->setAlias('fos_comment.spam_detection.comment', $config['service']['spam_detection']['comment']);

        $container->setAlias('fos_comment.manager.thread', $config['service']['manager']['thread']);
        $container->setAlias('fos_comment.manager.comment', $config['service']['manager']['comment']);
        $container->setAlias('fos_comment.manager.vote', $config['service']['manager']['vote']);
    }

    protected function loadAcl(ContainerBuilder $container, array $config)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('acl.xml');

        foreach (array(1 => 'create', 'view', 'edit', 'delete') as $index => $perm) {
            $container->getDefinition('fos_comment.acl.comment.roles')->replaceArgument($index, $config['acl_roles']['comment'][$perm]);
            $container->getDefinition('fos_comment.acl.thread.roles')->replaceArgument($index, $config['acl_roles']['thread'][$perm]);
            $container->getDefinition('fos_comment.acl.vote.roles')->replaceArgument($index, $config['acl_roles']['vote'][$perm]);
        }

        $container->setAlias('fos_comment.acl.thread', $config['service']['acl']['thread']);
        $container->setAlias('fos_comment.acl.comment', $config['service']['acl']['comment']);
        $container->setAlias('fos_comment.acl.vote', $config['service']['acl']['vote']);
    }
}
