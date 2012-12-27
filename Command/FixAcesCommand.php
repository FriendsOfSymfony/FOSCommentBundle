<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use FOS\CommentBundle\Model\VotableCommentInterface;

/**
 * This command installs global access control entries (ACEs)
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class FixAcesCommand extends ContainerAwareCommand
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

This will generally only happen when the CommentBundle has been used without acl for
a period of time or if comments have been added to the database without using proper
services for persisting them.
EOT
            );
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->getContainer()->has('security.acl.provider')) {
            $output->writeln('You must setup the ACL system, see the Symfony2 documentation for how to do this.');

            return;
        }

        $provider = $this->getContainer()->get('security.acl.provider');

        $threadAcl = $this->getContainer()->get('fos_comment.acl.thread');
        $threadManager = $this->getContainer()->get('fos_comment.manager.thread.default');

        $commentAcl = $this->getContainer()->get('fos_comment.acl.comment');
        $commentManager = $this->getContainer()->get('fos_comment.manager.comment.default');

        $voteAcl = $this->getContainer()->get('fos_comment.acl.vote');
        $voteManager = $this->getContainer()->get('fos_comment.manager.vote.default');

        $foundThreadAcls = 0;
        $foundCommentAcls = 0;
        $foundVoteAcls = 0;
        $createdThreadAcls = 0;
        $createdCommentAcls = 0;
        $createdVoteAcls = 0;

        foreach ($threadManager->findAllThreads() as $thread) {
            $oid = new ObjectIdentity($thread->getId(), get_class($thread));

            try {
                $provider->findAcl($oid);
                $foundThreadAcls++;
            } catch (AclNotFoundException $e) {
                $threadAcl->setDefaultAcl($thread);
                $createdThreadAcls++;
            }

            foreach ($commentManager->findCommentsByThread($thread) as $comment) {
                $comment_oid = new ObjectIdentity($comment->getId(), get_class($comment));

                try {
                    $provider->findAcl($comment_oid);
                    $foundCommentAcls++;
                } catch (AclNotFoundException $e) {
                    $commentAcl->setDefaultAcl($comment);
                    $createdCommentAcls++;
                }

                if ($comment instanceof VotableCommentInterface) {
                    foreach ($voteManager->findVotesByComment($comment) as $vote) {
                        $vote_oid = new ObjectIdentity($vote->getId(), get_class($vote));

                        try {
                            $provider->findAcl($vote_oid);
                            $foundVoteAcls++;
                        } catch (AclNotFoundException $e) {
                            $voteAcl->setDefaultAcl($vote);
                            $createdVoteAcls++;
                        }
                    }
                }
            }
        }

        $output->writeln("Found {$foundThreadAcls} Thread Acl Entries, Created {$createdThreadAcls} Thread Acl Entries");
        $output->writeln("Found {$foundCommentAcls} Comment Acl Entries, Created {$createdCommentAcls} Comment Acl Entries");
        $output->writeln("Found {$foundVoteAcls} Vote Acl Entries, Created {$createdVoteAcls} Vote Acl Entries");
    }
}
