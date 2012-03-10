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
        ), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW' => 'user',
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
        $this->client->insulate(true);

        $this->client->request('GET', '/comment_api/threads/non-existant.json');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testGetThreadFormAndSubmit()
    {
        $crawler = $this->client->request('GET', '/comment_api/threads/new.html');

        $this->assertEquals(
            'http://localhost/comment_api/threads',
            $crawler->filter('form.fos_comment_comment_form')->attr('action')
        );

        $id = 'test';

        $form = $crawler->selectButton('fos_comment_comment_new_submit')->form();
        $form['fos_comment_thread[id]'] = $id;
        // Note: the url validator fails with just http://localhost/
        $form['fos_comment_thread[permalink]'] = 'http://localhost.test/async/test';
        $this->client->submit($form);

        $this->assertRedirect($this->client->getResponse(), "/comment_api/threads/{$id}");
        var_dump($this->client->getContainer()->get('database_connection')->fetchAll('SELECT * FROM test_thread'));

        return $id;
    }

    /**
     * @param mixed $id
     * @depends testGetThreadFormAndSubmit
     */
    public function testGetThread($id)
    {
        var_dump($this->client->getContainer()->get('database_connection')->fetchAll('SELECT * FROM test_thread'));

        die;
        $crawler = $this->client->request('GET', "/comment_api/threads/{$id}");
        //var_dump((string) $this->client->getResponse()); die;
    }
}
