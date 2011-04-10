<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Entity;

use Doctrine\ORM\EntityManager;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\VotableCommentInterface;
use FOS\CommentBundle\Model\VoteInterface;
use FOS\CommentBundle\Model\VoteManager as BaseVoteManager;
use FOS\UserBundle\Model\UserInterface;

class VoteManager extends BaseVoteManager
{
    protected $em;
    protected $repository;
    protected $class;

    /**
     * Constructor.
     *
     * @param EntityManager     $em
     * @param string            $class
     */
    public function __construct(EntityManager $em, $class)
    {
        $this->em         = $em;
        $this->repository = $em->getRepository($class);
        $this->class      = $em->getClassMetadata($class)->name;
    }

    public function addVote(VoteInterface $vote, VotableCommentInterface $comment)
    {
        $vote->setComment($comment);
        $comment->setScore($comment->getScore() + $vote->getValue());

        $this->em->persist($comment);
        $this->em->persist($vote);
        $this->em->flush();
    }

    public function findVoteBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    public function findVotesByComment(CommentInterface $comment)
    {
        $qb = $this->repository->createQueryBuilder('v');
        $qb->join('v.Comment', 'c');
        $qb->andWhere('c.id = :commentId');
        $qb->setParameter('commentId', $comment->getId());

        $votes = $qb->getQuery()->execute();
        return $votes;
    }

    /*public function hasVoted(CommentInterface $comment, UserInterface $user)
    {
        if (!$vote = $this->createVote() AND !$vote instanceof SignedVoteInterface) {
            throw new RuntimeException('We cannot check if someone has voted when the Vote does not implement SignedVoteInterface!');
        }

        $qb = $this->repository->createQueryBuilder('v');
        $qb->join('v.Voter', 'vo');
        $qb->join('v.Comment', 'c');
        $qb->andWhere('c.id = :commentId');
        $qb->andWhere('vo.id = :userId');
        $qb->setParameters(array(
            'commentId' => $comment->getId(),
            'userId' => $user->getId(),
        ));

        $result = $qb->getQuery()->execute();

        if ($result) {
            return true;
        }

        return false;
    }*/

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
