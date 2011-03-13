<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentManagerAcceptanceTest extends WebTestCase
{
    public function testFindCommentsByThreadIdentifier()
    {
        $container      = $this->createClient()->getContainer();
        $commentManager = $container->get('fos_comment.manager.comment');

        $comments = $commentManager->findCommentsByThreadIdentifier('homepage');

        $firstComment = $comments[0]['comment'];
        $this->assertEquals('1 - First comment in root', $firstComment->getBody());

        $secondComment = $comments[1]['comment'];
        $this->assertEquals('2 - Second comment in root', $secondComment->getBody());

        $firstCommentChildren = $comments[0]['children'];
        $this->assertEquals(1, count($firstCommentChildren));

        $firstCommentChild = $comments[0]['children'][0]['comment'];
        $this->assertEquals('6 - First comment in comment 1', $firstCommentChild->getBody());

        $firstCommentChildChildren = $comments[0]['children'][0]['children'];
        $this->assertEquals(0, count($firstCommentChildChildren));

        $deepComment = $comments[1]['children'][0]['children'][0]['comment'];
        $this->assertEquals('5 - First comment in comment 3', $deepComment->getBody());
    }
}
