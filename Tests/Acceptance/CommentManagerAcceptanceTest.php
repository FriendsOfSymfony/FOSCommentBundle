<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentManagerAcceptanceTest extends WebTestCase
{
    public function testFindCommentsByThreadIdentifier()
    {
        $container      = $this->createClient()->getContainer();
        $threadManager  = $container->get('fos_comment.manager.thread');
        $commentManager = $container->get('fos_comment.manager.comment');

        $thread   = $threadManager->findThreadByIdentifier('homepage');
        $comments = $commentManager->findCommentsByThread($thread);

        /**
         * 2
         *  4
         *  3
         *   5
         * 1
         *  6
         */
        $secondComment = $comments[0]['comment'];
        $this->assertEquals('2 - Second comment in root', $secondComment->getBody());

        $firstComment = $comments[1]['comment'];
        $this->assertEquals('1 - First comment in root', $firstComment->getBody());

        $firstCommentChildren = $comments[1]['children'];
        $this->assertEquals(1, count($firstCommentChildren));

        $firstCommentChild = $comments[1]['children'][0]['comment'];
        $this->assertEquals('6 - First comment in comment 1', $firstCommentChild->getBody());

        $firstCommentChildChildren = $comments[1]['children'][0]['children'];
        $this->assertEquals(0, count($firstCommentChildChildren));

        $firstCommentInComment2 = $comments[0]['children'][0]['comment'];
        $this->assertEquals('4 - Second comment in comment 2', $firstCommentInComment2->getBody());

        $secondCommentInComment2 = $comments[0]['children'][1]['comment'];
        $this->assertEquals('3 - First comment in comment 2', $secondCommentInComment2->getBody());

        $deepComment = $comments[0]['children'][1]['children'][0]['comment'];
        $this->assertEquals('5 - First comment in comment 3', $deepComment->getBody());
    }
}
