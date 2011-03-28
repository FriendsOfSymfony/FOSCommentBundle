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
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

/**
 * This command installs global access control entries (ACEs)
 *
 * @author Tim Nagel <tim@nagel.com.au>
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class InstallAcesCommand extends BaseCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('fos:comment:installAces')
            ->setDescription('Installs global ACEs')
            ->setDefinition(array(
                new InputOption('flush', null, InputOption::VALUE_NONE, 'Flush existing Acls'),
            ))
            ->setHelp(<<<EOT
This command should be run once during the installation process of the entire bundle or after enabling Acl for the first time.
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

        // TODO: this command should allow different ACL settings to be implemented
        // EG: Anon can post, user can edit, etc

        $provider = $this->container->get('security.acl.provider');

        if (!!$input->getOption('flush')) {
            $output->writeln('Flushing Global ACEs');
            $this->deleteCommentAces($provider, $output);
            $this->deleteThreadAces($provider, $output);
        }

        $this->installCommentAces($provider, $output);
        $this->installThreadAces($provider, $output);
        $this->fixNonexistantAces($provider, $output);

        $output->writeln('Global ACEs have been installed.');
    }

    /**
     * Installs the Comment Aces.
     *
     * @param AclProviderInterface $provider
     * @param OutputInterface $output
     * @return void
     */
    protected function installCommentAces(MutableAclProvider $provider, OutputInterface $output)
    {
        $oid = new ObjectIdentity('class', $this->container->get('fos_comment.manager.comment')->getClass());

        try {
            $acl = $provider->createAcl($oid);
        } catch (AclAlreadyExistsException $exists) {
            $output->writeln('The Comment Aces are already installed.');
            return;
        }

        $builder = new MaskBuilder();

        $builder->add('iddqd');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_SUPERADMIN'), $builder->get());

        $builder->reset();
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'), $builder->get());

        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_USER'), $builder->get());

        $provider->updateAcl($acl);
    }

    /**
     * Installs the Thread Aces.
     *
     * @param AclProviderInterface $provider
     * @param OutputInterface $output
     * @return void
     */
    protected function installThreadAces(MutableAclProvider $provider, OutputInterface $output)
    {
        $oid = new ObjectIdentity('class', $this->container->get('fos_comment.manager.thread')->getClass());

        try {
            $acl = $provider->createAcl($oid);
        } catch (AclAlreadyExistsException $exists) {
            $output->writeln('The Thread Aces are already installed.');
            return;
        }

        $builder = new MaskBuilder();

        $builder->add('iddqd');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_SUPERADMIN'), $builder->get());

        $builder->reset();
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('IS_AUTHENTICATED_ANONYMOUSLY'), $builder->get());

        // Note: if a user is able to create a comment they must be able to create a thread.
        $builder->reset();
        $builder->add('create');
        $builder->add('view');
        $acl->insertClassAce(new RoleSecurityIdentity('ROLE_USER'), $builder->get());

        $provider->updateAcl($acl);
    }

    protected function deleteCommentAces(MutableAclProvider $provider, OutputInterface $output)
    {
        $oid = new ObjectIdentity('class', $this->container->get('fos_comment.manager.comment')->getClass());
        $provider->deleteAcl($oid);
    }

    protected function deleteThreadAces(MutableAclProvider $provider, OutputInterface $output)
    {
        $oid = new ObjectIdentity('class', $this->container->get('fos_comment.manager.thread')->getClass());
        $provider->deleteAcl($oid);
    }

    protected function fixNonexistantAces(MutableAclProvider $provider, OutputInterface $output)
    {
        $threadAcl = $this->container->get('fos_comment.acl.thread.security');
        $threadManager = $this->container->get('fos_comment.manager.default.thread');

        $commentAcl = $this->container->get('fos_comment.acl.comment.security');
        $commentManager = $this->container->get('fos_comment.manager.default.comment');

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
