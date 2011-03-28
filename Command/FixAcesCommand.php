<?php

/**
 * This file is part of the FOS\CommentBundle.
 *
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Command;

use FOS\UserBundle\Model\User;
use Symfony\Bundle\FrameworkBundle\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * This command creates Ace entries for all Comment and Thread entities that
 * don't have one. Useful if Acl is enabled after using this bundle for a period
 * of time.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class FixAcesCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:comment:fixAces')
            ->setDescription('Fixes object ACEs')
            ->setHelp(<<<EOT
The <info>fos:comment:fixAces</info> command will go through all CommentBundle objects and make sure that they have appropriate Acl entries.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->container->has('security.acl.provider')) {
            $output->writeln('You must setup the ACL system, see the Symfony2 documentation for how to do this.');
            return;
        }


        // Loop over all Comments and Threads, checking if Acl exists or not.
        $provider = $this->container->get('security.acl.provider');
        $threadAcl = $this->container->get('fos_comment.acl.thread.security');
        $threadManager = $this->container->get('fos_comment.manager.default.thread');

        $commentAcl = $this->container->get('fos_comment.acl.comment.security');
        $commentManager = $this->container->get('fos_comment.manager.default.comment');

        $threads = $threadManager->findAllThreads();

        foreach ($threads AS $thread) {
            $oid = new ObjectIdentity($thread->getIdentifier(), get_class($thread));

            try {
                $provider->findAcl($oid);
            }
            catch (AclNotFoundException $e) {
                $output->writeln("Building Acl for {$oid}");
                $threadAcl->setDefaultAcl($thread);
            }

            foreach ($commentManager->findCommentTreeByThread($thread) AS $comment) {
                $comment_oid = new ObjectIdentity($comment->getId(), get_class($comment));

                try {
                    $provider->findAcl($comment_oid);
                }
                catch (AclNotFoundException $e) {
                    $output->writeln("Building Acl for {$comment_oid}");
                    $commentAcl->setDefaultAcl($comment);
                }
            }
        }

        die;

    }
}
