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
use FOS\CommentBundle\Model\CommentManagerInterface;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;

/**
 * This command installs global access control entries (ACEs).
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class FixAcesCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'fos:comment:fixAces';

    /**
     * @var AclProviderInterface
     */
    private $provider;

    /**
     * @var CommentAclInterface
     */
    private $commentAcl;

    /**
     * @var CommentManagerInterface
     */
    private $commentManager;

    /**
     * @var ThreadAclInterface
     */
    private $threadAcl;

    /**
     * @var ThreadManagerInterface
     */
    private $threadManager;

    /**
     * @var VoteAclInterface
     */
    private $voteAcl;

    /**
     * @var VoteManagerInterface
     */
    private $voteManager;

    /**
     * @param AclProviderInterface    $provider
     * @param CommentAclInterface     $commentAcl
     * @param CommentManagerInterface $commentManager
     * @param ThreadAclInterface      $threadAcl
     * @param ThreadManagerInterface  $threadManager
     * @param VoteAclInterface        $voteAcl
     * @param VoteManagerInterface    $voteManager
     */
    public function __construct(
        AclProviderInterface $provider,
        CommentAclInterface $commentAcl,
        CommentManagerInterface $commentManager,
        ThreadAclInterface $threadAcl,
        ThreadManagerInterface $threadManager,
        VoteAclInterface $voteAcl,
        VoteManagerInterface $voteManager
    ) {
        parent::__construct();

        $this->provider = $provider;
        $this->commentAcl = $commentAcl;
        $this->commentManager = $commentManager;
        $this->threadAcl = $threadAcl;
        $this->threadManager = $threadManager;
        $this->voteAcl = $voteAcl;
        $this->voteManager = $voteManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName(static::$defaultName) // BC with 2.8
            ->setDescription('Fixes Object Ace entries')
            ->setHelp(<<<'EOT'
This command will fix all Ace entries for existing objects. This command only needs to
be run when there are Objects that do not have Ace entries.

This will generally only happen when the CommentBundle has been used without acl for
a period of time or if comments have been added to the database without using proper
services for persisting them.
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $foundThreadAcls = 0;
        $foundCommentAcls = 0;
        $foundVoteAcls = 0;
        $createdThreadAcls = 0;
        $createdCommentAcls = 0;
        $createdVoteAcls = 0;

        foreach ($this->threadManager->findAllThreads() as $thread) {
            $oid = new ObjectIdentity($thread->getId(), get_class($thread));

            try {
                $this->provider->findAcl($oid);
                ++$foundThreadAcls;
            } catch (AclNotFoundException $e) {
                $this->threadAcl->setDefaultAcl($thread);
                ++$createdThreadAcls;
            }

            foreach ($this->commentManager->findCommentsByThread($thread) as $comment) {
                $comment_oid = new ObjectIdentity($comment->getId(), get_class($comment));

                try {
                    $this->provider->findAcl($comment_oid);
                    ++$foundCommentAcls;
                } catch (AclNotFoundException $e) {
                    $this->commentAcl->setDefaultAcl($comment);
                    ++$createdCommentAcls;
                }

                if ($comment instanceof VotableCommentInterface) {
                    foreach ($this->voteManager->findVotesByComment($comment) as $vote) {
                        $vote_oid = new ObjectIdentity($vote->getId(), get_class($vote));

                        try {
                            $this->provider->findAcl($vote_oid);
                            ++$foundVoteAcls;
                        } catch (AclNotFoundException $e) {
                            $this->voteAcl->setDefaultAcl($vote);
                            ++$createdVoteAcls;
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
