<?php

namespace FOS\CommentBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;

class ApiTest extends WebTestCase
{
    protected function setUp()
    {
        $this->client = self::createClient(array(
            'test_case' => 'Basic',
            'root_config' => 'config.yml'
        ));

        parent::setUp();
    }

    /**
     * fos_comment_new_thread_comment_votes: GET: /comment_api/threads/{id}/comments/{commentId}/votes/new.{_format}
     * fos_comment_new_thread_comments: GET: /comment_api/threads/{id}/comments/new.{_format}
     * fos_comment_new_threads: GET: /comment_api/threads/new.{_format}
     * fos_comment_get_thread: GET: /comment_api/threads/{id}.{_format}
     * fos_comment_get_thread_comment: GET: /comment_api/threads/{id}/comments/{commentId}.{_format}
     * fos_comment_get_thread_comment_votes: GET: /comment_api/threads/{id}/comments/{commentId}/votes.{_format}
     * fos_comment_get_thread_comments: GET: /comment_api/threads/{id}/comments.{_format}
     * fos_comment_post_thread_comment_votes: POST: /comment_api/threads/{id}/comments/{commentId}/votes.{_format}
     * fos_comment_post_thread_comments: POST: /comment_api/threads/{id}/comments.{_format}
     * fos_comment_post_threads: POST: /comment_api/threads.{_format}
     */

    public function testGetThread404()
    {
        /**
         * TODO: This request causes an uncaught exception in the phpunit output..
         *
         * $this->client->request('GET', '/comment_api/threads/non-existant.json');
         * $this->assertEquals(404, $this->client->getResponse()->getStatus());
         */
    }

    public function testGetThreadFormAndSubmit()
    {
        $crawler = $this->client->request('GET', '/comment_api/threads/new.html');

        $this->assertEquals(
            'http://localhost/comment_api/threads',
            $crawler->filter('form.fos_comment_comment_form')->attr('action')
        );

        /*$form = $crawler->selectButton('fos_comment_comment_new_submit')->form();
        $form['fos_comment_thread[id]'] = 'test';
        $form['fos_comment_thread[permalink]'] = '/async/test';

        $crawler = $this->client->submit($form);*/
    }

    public function testAsync()
    {
        $crawler = $this->client->request('GET', '/async/test');

        $this->assertEquals(1, $crawler->filter('#fos_comment_thread')->count());
        $this->assertContains('fos_comment_script.src', $crawler->filter('script')->text());
    }

    public function testInline()
    {
        $crawler = $this->client->request('GET', '/inline/test');

        $this->assertEquals(1, $crawler->filter('#fos_comment_thread')->count());
    }
}
