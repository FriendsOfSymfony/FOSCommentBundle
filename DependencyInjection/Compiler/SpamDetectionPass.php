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

/**
 * SpamDetectionPass
 */
class SpamDetectionPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        // Symfony 2.3 BC
        if (class_exists('Symfony\Component\HttpFoundation\RequestStack')) {
            return;
        }

        if ($container->hasDefinition('fos_comment.spam_detection.comment.akismet')) {
            $definition = $container->getDefinition('fos_comment.spam_detection.comment.akismet');
            $definition->setScope('request');
        }

        if ($container->hasDefinition('fos_comment.listener.comment_spam')) {
            $definition = $container->getDefinition('fos_comment.listener.comment_spam');
            $definition->setScope('request');
        }
    }
}
