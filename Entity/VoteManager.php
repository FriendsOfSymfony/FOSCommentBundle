<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Entity;

use Doctrine\ORM\EntityManager;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VoteManager as BaseVoteManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Default ORM VoteManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class VoteManager extends BaseVoteManager
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @param \Doctrine\ORM\EntityManager                                 $em
     * @param $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, EntityManager $em, $class)
    {
        parent::__construct($dispatcher);

        $this->em = $em;
        $this->repository = $em->getRepository($class);

        $metadata = $em->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Finds a vote by specified criteria.
     *
     * @param array $criteria
     *
     * @return VoteInterface
     */
    public function findVoteBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Finds all votes belonging to a comment.
     *
     * @param \FOS\CommentBundle\Model\VotableCommentInterface $comment
     *
     * @return array|null
     */
    public function findVotesByComment(VotableCommentInterface $comment)
    {
        $qb = $this->repository->createQueryBuilder('v');
        $qb->join('v.comment', 'c');
        $qb->andWhere('c.id = :commentId');
        $qb->setParameter('commentId', $comment->getId());

        $votes = $qb->getQuery()->execute();

        return $votes;
    }

    /**
     * {@inheritdoc}
     */
    public function findVotesByCommentThreadAndVoter(VotableCommentInterface $comment, UserInterface $voter)
    {
        $qb = $this->repository->createQueryBuilder('v');
        $qb->join('v.comment', 'c');
        $qb->andWhere('(c.thread) = :thread');
        $qb->andWhere('(v.voter) = :voter');
        $qb->setParameter('thread', $comment->getThread());
        $qb->setParameter('voter', $voter);

        $votes = $qb->getQuery()->execute();

        return $votes;
    }

    /**
     * {@inheritdoc}
     */
    public function isNewVote(VoteInterface $vote)
    {
        return !$this->em->getUnitOfWork()->isInIdentityMap($vote);
    }

    /**
     * Returns the fully qualified comment vote class name.
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Persists a vote.
     *
     * @param \FOS\CommentBundle\Model\VoteInterface $vote
     */
    protected function doSaveVote(VoteInterface $vote)
    {
        $this->em->persist($vote->getComment());
        $this->em->persist($vote);
        $this->em->flush();
    }

    /**
     * @param VoteInterface $vote
     */
    protected function doRemoveVote(VoteInterface $vote)
    {
        $this->em->persist($vote->getComment());
        $this->em->remove($vote);
        $this->em->flush();
    }
}
