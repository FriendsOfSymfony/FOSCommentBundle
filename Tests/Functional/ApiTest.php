<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Functional;

/**
 * Functional tests of the CommentBundle api.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 * @group functional
 */
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
     * Tests retrieval of a thread that doesnt exist.
     *
     * fos_comment_get_thread: GET: /comment_api/threads/{id}.{_format}
     */
    public function testGetThread404()
    {
        $this->client->insulate(true);

        $this->client->request('GET', '/comment_api/threads/non-existant.json');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Tests creation of a new form.retrieval of a thread that doesnt exist.
     *
     * fos_comment_new_threads: GET: /comment_api/threads/new.{_format}
     * fos_comment_post_threads: POST: /comment_api/threads.{_format}
     *
     * @return string The id of the created thread
     */
    public function testGetThreadFormAndSubmit()
    {
        $crawler = $this->client->request('GET', '/comment_api/threads/new.html');

        $this->assertEquals(
            'http://localhost/comment_api/threads',
            $crawler->filter('form.fos_comment_comment_form')->attr('action')
        );

        $id = uniqid();

        $form = $crawler->selectButton('fos_comment_comment_new_submit')->form();
        $form['fos_comment_thread[id]'] = $id;
        // Note: the url validator fails with just http://localhost/
        $form['fos_comment_thread[permalink]'] = "http://localhost.test/async/{$id}";
        $this->client->submit($form);

        $this->assertRedirect($this->client->getResponse(), "/comment_api/threads/{$id}");

        return $id;
    }

    /**
     * Tests retrieval of an existing thread.
     *
     * fos_comment_get_thread: GET: /comment_api/threads/{id}.{_format}
     *
     * @param mixed $id
     * @depends testGetThreadFormAndSubmit
     */
    public function testGetThread($id)
    {
        $this->client->request('GET', "/comment_api/threads/{$id}.json");

        $this->assertContains($id, (string) $this->client->getResponse()->getContent());
    }

    /**
     * Tests retrieval of an empty thread.
     *
     * fos_comment_post_thread_comments: POST: /comment_api/threads/{id}/comments.{_format}
     *
     * @param mixed $id
     * @depends testGetThreadFormAndSubmit
     */
    public function testGetEmptyThread($id)
    {
        $crawler = $this->client->request('GET', "/comment_api/threads/{$id}/comments.html");

        $this->assertCount(0, $crawler->filter('.fos_comment_comment_body'));

        return $id;
    }

    /**
     * Tests addition of a comment to a thread.
     *
     * fos_comment_new_thread_comments: GET: /comment_api/threads/{id}/comments/new.{_format}
     * fos_comment_post_thread_comments: POST: /comment_api/threads/{id}/comments.{_format}
     * fos_comment_get_thread_comment: GET: /comment_api/threads/{id}/comments/{commentId}.{_format}
     *
     * @param mixed $id
     * @depends testGetEmptyThread
     */
    public function testAddCommentToThread($id)
    {
        $crawler = $this->client->request('GET', "/comment_api/threads/{$id}/comments/new.html");

        $form = $crawler->selectButton('fos_comment_comment_new_submit')->form();
        $form['fos_comment_comment[body]'] = 'Test Comment';
        $this->client->submit($form);

        $this->assertRedirect($this->client->getResponse(), "/comment_api/threads/{$id}/comments/1");
        $crawler = $this->client->followRedirect();

        $this->assertContains('Test Comment', $crawler->filter('.fos_comment_comment_body')->text());

        return $id;
    }

    /**
     * Replies to an existing comment.
     *
     * fos_comment_get_thread_comments: GET: /comment_api/threads/{id}/comments.{_format}
     * fos_comment_new_thread_comments: GET: /comment_api/threads/{id}/comments/new.{_format}
     * fos_comment_get_thread_comment: GET: /comment_api/threads/{id}/comments/{commentId}.{_format}
     *
     * @param mixed $id
     * @depends testAddCommentToThread
     */
    public function testReplyToComment($id)
    {
        //todo: is there a cleaner/faster way for this?
        // sleep a second to create different 'createdAt' dates
        sleep(1);

        $crawler = $this->client->request('GET', "/comment_api/threads/{$id}/comments.html");

        $parentId = $crawler->filter('.fos_comment_comment_reply_show_form')->first()->attr('data-parent-id');

        $crawler = $this->client->request('GET', "/comment_api/threads/{$id}/comments/new.html", array(
            'parentId' => $parentId,
        ));

        $form = $crawler->selectButton('fos_comment_comment_new_submit')->form();
        $form['fos_comment_comment[body]'] = 'Test Reply Comment';
        $this->client->submit($form);

        $this->assertRedirect($this->client->getResponse(), "/comment_api/threads/{$id}/comments/2");
        $crawler = $this->client->followRedirect();

        $this->assertContains('Test Reply Comment', $crawler->filter('.fos_comment_comment_body')->text());

        return $id;
    }

    /**
     * Tests that there are 2 comments in a tree.
     *
     * fos_comment_get_thread_comments: GET: /comment_api/threads/{id}/comments.{_format}
     *
     * @param $id
     * @depends testReplyToComment
     */
    public function testGetCommentTree($id)
    {
        $crawler = $this->client->request('GET', "/comment_api/threads/{$id}/comments.html");

        $this->assertCount(2, $crawler->filter('.fos_comment_comment_body'));
        $this->assertContains('Test Reply Comment', $crawler->filter('.fos_comment_comment_show .fos_comment_comment_depth_1 .fos_comment_comment_body')->first()->text());
    }

    /**
     * Tests that there is only 1 comment in the tree.
     *
     * fos_comment_get_thread_comments: GET: /comment_api/threads/{id}/comments.{_format}?displayDepth=0
     *
     * @param $id
     * @depends testReplyToComment
     */
    public function testGetCommentTreeDepth($id)
    {
        $crawler = $this->client->request('GET', "/comment_api/threads/{$id}/comments.html?displayDepth=0");

        $this->assertCount(1, $crawler->filter('.fos_comment_comment_body'));
        $this->assertContains('Test Comment', $crawler->filter('.fos_comment_comment_body')->first()->text());
        $this->assertContains('Test Comment', $crawler->filter('.fos_comment_comment_body')->last()->text());
    }

    /**
     * Tests that there are 2 comments in a thread. Rendered both on level 0.
     *
     * fos_comment_get_thread_comments: GET: /comment_api/threads/{id}/comments.{_format}?view=flat
     *
     * @param $id
     * @depends testReplyToComment
     */
    public function testGetCommentFlat($id)
    {
        $crawler = $this->client->request('GET', "/comment_api/threads/{$id}/comments.html?view=flat");

        $this->assertCount(2, $crawler->filter('.fos_comment_comment_body'));
        $this->assertContains('Test Comment', $crawler->filter('.fos_comment_comment_show.fos_comment_comment_depth_0 .fos_comment_comment_body')->first()->text());
        $this->assertContains('Test Reply Comment', $crawler->filter('.fos_comment_comment_show.fos_comment_comment_depth_0 .fos_comment_comment_body')->last()->text());
    }

    /**
     * Tests that there are 2 comments in a thread. Rendered both on level 0. Sorted by date asc/desc.
     *
     * fos_comment_get_thread_comments: GET: /comment_api/threads/{id}/comments.{_format}?view=flat&sorter=date_asc/date_desc
     *
     * @param $id
     * @depends testReplyToComment
     */
    public function testGetCommentFlatSorted($id)
    {
        $crawler = $this->client->request('GET', "/comment_api/threads/{$id}/comments.html?view=flat&sorter=date_desc");
        $crawler2 = $this->client->request('GET', "/comment_api/threads/{$id}/comments.html?view=flat&sorter=date_asc");

        $this->assertCount(2, $crawler->filter('.fos_comment_comment_body'));
        $this->assertCount(2, $crawler2->filter('.fos_comment_comment_body'));
        $this->assertContains('Test Reply Comment', $crawler->filter('.fos_comment_comment_show.fos_comment_comment_depth_0 .fos_comment_comment_body')->first()->text());
        $this->assertContains('Test Comment', $crawler->filter('.fos_comment_comment_show.fos_comment_comment_depth_0 .fos_comment_comment_body')->last()->text());

        $this->assertEquals(
            $crawler->filter('.fos_comment_comment_show.fos_comment_comment_depth_0 .fos_comment_comment_body')->first()->text(),
            $crawler2->filter('.fos_comment_comment_show.fos_comment_comment_depth_0 .fos_comment_comment_body')->last()->text()
        );

        $this->assertEquals(
            $crawler->filter('.fos_comment_comment_show.fos_comment_comment_depth_0 .fos_comment_comment_body')->last()->text(),
            $crawler2->filter('.fos_comment_comment_show.fos_comment_comment_depth_0 .fos_comment_comment_body')->first()->text()
        );
    }
}
