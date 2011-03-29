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

use Symfony\Bundle\FrameworkBundle\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;

/**
 * This command installs global access control entries (ACEs)
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class FixAcesCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:comment:fixAces')
            ->setDescription('Fixes Object Ace entries')
            ->setHelp(<<<EOT
This command will fix all Ace entries for existing objects. This command only needs to
be run when there are Objects that do not have Ace entries.
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

        $threadAcl = $this->container->get('fos_comment.acl.thread');
        $threadManager = $this->container->get('fos_comment.manager.thread.default');

        $commentAcl = $this->container->get('fos_comment.acl.comment');
        $commentManager = $this->container->get('fos_comment.manager.comment.default');

        $foundThreadAcls = 0;
        $foundCommentAcls = 0;
        $createdThreadAcls = 0;
        $createdCommentAcls = 0;

        foreach ($threadManager->findAllThreads() AS $thread) {
            $oid = new ObjectIdentity($thread->getIdentifier(), get_class($thread));

            try {
                $provider->findAcl($oid);
                $foundThreadAcls++;
            }
            catch (AclNotFoundException $e) {
                $threadAcl->setDefaultAcl($thread);
                $createdThreadAcls++;
            }

            foreach ($commentManager->findCommentsByThread($thread) AS $comment) {
                $comment_oid = new ObjectIdentity($comment->getId(), get_class($comment));

                try {
                    $provider->findAcl($comment_oid);
                    $foundCommentAcls++;
                }
                catch (AclNotFoundException $e) {
                    $commentAcl->setDefaultAcl($comment);
                    $createdCommentAcls++;
                }
            }
        }

        $output->writeln("Found {$foundThreadAcls} Thread Acl Entries, Created {$createdThreadAcls} Thread Acl Entries");
        $output->writeln("Found {$foundCommentAcls} Comment Acl Entries, Created {$createdCommentAcls} Comment Acl Entries");
    }
}