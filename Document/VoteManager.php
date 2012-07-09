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
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VoteManager as BaseVoteManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
     * @param DocumentManager $dm
     * @param string          $class
     */
    public function __construct(EventDispatcherInterface $dispatcher, DocumentManager $dm, $class)
    {
        parent::__construct($dispatcher);

        $this->dm = $dm;
        $this->repository = $dm->getRepository($class);

        $metadata = $dm->getClassMetadata($class);
        $this->class = $metadata->name;
    }

    /**
     * Persists a vote.
     *
     * @param \FOS\CommentBundle\Model\VoteInterface $vote
     */
    protected function doSaveVote(VoteInterface $vote)
    {
        $this->dm->persist($vote->getComment());
        $this->dm->persist($vote);
        $this->dm->flush();
    }

    /**
     * Finds a vote by specified criteria.
     *
     * @param  array         $criteria
     * @return VoteInterface
     */
    public function findVoteBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    /**
     * Finds all votes belonging to a comment.
     *
     * @param  \FOS\CommentBundle\Model\VotableCommentInterface $comment
     * @return array|null
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
