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

/**
 * SecurityPass
 *
 * To be removed when dependency on Symfony DependencyInjection is bumped to 2.6
 */
class SecurityPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->has('security.authorization_checker')) {
            $authorizationCheckerReference = new Reference('security.authorization_checker');
        } else {
            $authorizationCheckerReference = new Reference('security.context');
        }

        if ($container->has('security.token_storage')) {
            $tokenStorageReference = new Reference('security.token_storage');
        } else {
            $tokenStorageReference = new Reference('security.context');
        }

        $container->getDefinition('fos_comment.listener.comment_blamer')
            ->replaceArgument(0, $authorizationCheckerReference)
            ->replaceArgument(1, $tokenStorageReference);

        $container->getDefinition('fos_comment.listener.vote_blamer')
            ->replaceArgument(0, $authorizationCheckerReference)
            ->replaceArgument(1, $tokenStorageReference);

        if ($container->has('fos_comment.acl.thread.security')) {
            $container->getDefinition('fos_comment.acl.thread.security')
                ->replaceArgument(0, $authorizationCheckerReference);
        }

        if ($container->has('fos_comment.acl.comment.security')) {
            $container->getDefinition('fos_comment.acl.comment.security')
                ->replaceArgument(0, $authorizationCheckerReference);
        }

        if ($container->has('fos_comment.acl.vote.security')) {
            $container->getDefinition('fos_comment.acl.vote.security')
                ->replaceArgument(0, $authorizationCheckerReference);
        }

        if ($container->has('fos_comment.acl.thread.roles')) {
            $container->getDefinition('fos_comment.acl.thread.roles')
                ->replaceArgument(0, $authorizationCheckerReference);
        }

        if ($container->has('fos_comment.acl.comment.roles')) {
            $container->getDefinition('fos_comment.acl.comment.roles')
                ->replaceArgument(0, $authorizationCheckerReference);
        }

        if ($container->has('fos_comment.acl.vote.roles')) {
            $container->getDefinition('fos_comment.acl.vote.roles')
                ->replaceArgument(0, $authorizationCheckerReference);
        }
    }
}
