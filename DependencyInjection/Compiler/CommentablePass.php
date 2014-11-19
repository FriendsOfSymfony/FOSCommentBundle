<?php
namespace FOS\CommentBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * CommentablePass
 *
 */
class CommentablePass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->has('fos_comment.listener.dynamic_relations') && $container->hasParameter('fos_comment.model.thread.class')) {
            $container->getDefinition('fos_comment.listener.dynamic_relations')
            	->replaceArgument(0,  $container->getParameter('fos_comment.model.thread.class'));
        }
    }
}