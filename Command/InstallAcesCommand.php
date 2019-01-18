<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Command;

use FOS\CommentBundle\Acl\CommentAclInterface;
use FOS\CommentBundle\Acl\ThreadAclInterface;
use FOS\CommentBundle\Acl\VoteAclInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * This command installs global access control entries (ACEs).
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class InstallAcesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'fos:comment:installAces';

    /**
     * @var CommentAclInterface
     */
    private $commentAcl;

    /**
     * @var ThreadAclInterface
     */
    private $threadAcl;

    /**
     * @var VoteAclInterface
     */
    private $voteAcl;

    /**
     * @param CommentAclInterface $commentAcl
     * @param ThreadAclInterface  $threadAcl
     * @param VoteAclInterface    $voteAcl
     */
    public function __construct(
        CommentAclInterface $commentAcl,
        ThreadAclInterface $threadAcl,
        VoteAclInterface $voteAcl
    ) {
        parent::__construct();

        $this->commentAcl = $commentAcl;
        $this->threadAcl = $threadAcl;
        $this->voteAcl = $voteAcl;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(static::$defaultName) // BC with 2.8
            ->setDescription('Installs global ACEs')
            ->setDefinition(array(
                new InputOption('flush', null, InputOption::VALUE_NONE, 'Flush existing Acls'),
            ))
            ->setHelp(<<<'EOT'
This command should be run once during the installation process of the entire bundle or
after enabling Acl for the first time.

If you have been using CommentBundle previously without Acl and are just enabling it, you
will also need to run fos:comment:fixAces.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('flush')) {
            $output->writeln('Flushing Global ACEs');

            $this->threadAcl->uninstallFallbackAcl();
            $this->commentAcl->uninstallFallbackAcl();
            $this->voteAcl->uninstallFallbackAcl();
        }

        $this->threadAcl->installFallbackAcl();
        $this->commentAcl->installFallbackAcl();
        $this->voteAcl->installFallbackAcl();

        $output->writeln('Global ACEs have been installed.');
    }
}
