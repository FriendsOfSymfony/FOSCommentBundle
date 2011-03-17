<?php

namespace FOS\CommentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use FOS\CommentBundle\Document\Comment;

class LoadCommentData implements FixtureInterface, OrderedFixtureInterface, ContainerAwareInterface
{
    protected $objectManager;
    protected $commentThreadManager;
    protected $commentManager;

    public function getOrder()
    {
        return 3;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->objectManager  = $container->get('doctrine.orm.entity_manager');
        $this->threadManager  = $container->get('fos_comment.manager.thread');
        $this->commentManager = $container->get('fos_comment.manager.comment');
    }

    public function load($manager)
    {
        $homepageThread = $this->threadManager->createThread();
        $homepageThread->setIdentifier('homepage');
        $this->objectManager->persist($homepageThread);

        /**
         * 2
         *  4
         *  3
         *   5
         * 1
         *  6
         */
        $comment1 = $this->commentManager->createComment();
        $comment1->setBody('1 - First comment in root');
        $comment1->setThread($homepageThread);
        $this->commentManager->addComment($comment1);
        $this->objectManager->flush();

        $comment2 = $this->commentManager->createComment();
        $comment2->setBody('2 - Second comment in root');
        $comment2->setThread($homepageThread);
        $this->commentManager->addComment($comment2);
        $this->objectManager->flush();

        $comment3 = $this->commentManager->createComment();
        $comment3->setBody('3 - First comment in comment 2');
        $comment3->setThread($homepageThread);
        $this->commentManager->addComment($comment3, $comment2);
        $this->objectManager->flush();

        $comment4 = $this->commentManager->createComment();
        $comment4->setBody('4 - Second comment in comment 2');
        $comment4->setThread($homepageThread);
        $this->commentManager->addComment($comment4, $comment2);
        $this->objectManager->flush();

        $comment5 = $this->commentManager->createComment();
        $comment5->setBody('5 - First comment in comment 3');
        $comment5->setThread($homepageThread);
        $this->commentManager->addComment($comment5, $comment3);
        $this->objectManager->flush();

        $comment6 = $this->commentManager->createComment();
        $comment6->setBody('6 - First comment in comment 1');
        $comment6->setThread($homepageThread);
        $this->commentManager->addComment($comment6, $comment1);
        $this->objectManager->flush();

        // Empty thread

        $articleThread = $this->threadManager->createThread();
        $articleThread->setIdentifier('article:23');
        $this->objectManager->persist($articleThread);

        $this->objectManager->flush();
    }
}
