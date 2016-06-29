<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Entity;

use Doctrine\ORM\EntityManager;
use FOS\CommentBundle\Event\FlagEvent;
use FOS\CommentBundle\Events;
use FOS\CommentBundle\Model\FlaggableCommentInterface;
use FOS\CommentBundle\Model\FlagInterface;
use FOS\CommentBundle\Model\FlagManager as BaseFlagManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Default ORM FlagManager.
 *
 * @author Hubert Bryłkowski <hubert@brylkowski.com>
 */
class FlagManager extends BaseFlagManager
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

    public function createFlag(FlaggableCommentInterface $comment)
    {
        $class = $this->getClass();
        $flag = new $class();
        $flag->setComment($comment);

        $event = new FlagEvent($flag);
        $this->dispatcher->dispatch(Events::FLAG_CREATE, $event);

        return $flag;

    }

    /**
     * Finds a flag by specified criteria.
     *
     * @param  array         $criteria
     * @return FlagInterface
     */
    public function findFlagBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }


    /**
     * Returns the fully qualified comment flag class name
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Finds all flags for a comment.
     *
     * @param  FlaggableCommentInterface $comment
     * @return FlagInterface[]
     */
    public function findFlagsByComment(FlaggableCommentInterface $comment)
    {
        $qb = $this->repository->createQueryBuilder('f')
            ->join('f.comment', 'c')
            ->where('c.id = :comment')
            ->setParameter('comment', $comment->getId());
        $votes = $qb->getQuery()->execute();

        return $votes;
    }

    public function findFlagById($id)
    {
        return $this->findFlagBy(['id' => $id]);
    }

    protected function doSaveFlag(FlagInterface $flag)
    {
        $this->em->persist($flag->getComment());
        $this->em->persist($flag);
        $this->em->flush();
    }

}
