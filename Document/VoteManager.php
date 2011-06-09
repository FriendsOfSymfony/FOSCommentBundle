<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Document;

use Doctrine\ODM\MongoDB\DocumentManager;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VoteManager as BaseVoteManager;
use FOS\UserBundle\Model\UserInterface;

/**
 * Default ODM VoteManager.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class VoteManager extends BaseVoteManager
{
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var DocumentRepository
     */
    protected $repository;

    /**
     * @var string
     */
    protected $class;

    /**
     * Constructor.
     *
     * @param DocumentManager   $dm
     * @param string            $class
     */
    public function __construct(DocumentManager $dm, $class)
    {
        $this->em         = $dm;
        $this->repository = $dm->getRepository($class);
        $this->class      = $dm->getClassMetadata($class)->name;
    }

    /**
     * Persists a vote.
     *
     * @param VoteInterface $vote
     * @param VotableCommentInterface $comment
     * @return void
     */
    public function addVote(VoteInterface $vote, VotableCommentInterface $comment)
    {
        $vote->setComment($comment);
        $comment->setScore($comment->getScore() + $vote->getValue());

        $this->dm->persist($comment);
        $this->dm->persist($vote);
        $this->dm->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function findVoteBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Finds all votes belonging to a comment.
     *
     * @param VotableCommentInterface $comment
     * @return array of VoteInterface
     */
    public function findVotesByComment(VotableCommentInterface $comment)
    {
        $qb = $this->repository->createQueryBuilder();
        $qb->field('comment.$id')->equals($comment->getId());
        $qb->sort('createdAt', 'ASC');

        $votes = $qb->getQuery()->execute();
        return $votes;
    }

    /**
     * Returns the fully qualified comment vote class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
