<?php

namespace FOS\CommentBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;

/**
 * @group functional
 */
class ThreadTest extends WebTestCase
{
    protected function setUp()
    {
        $this->client = self::createClient(array(
            'test_case' => 'Basic',
            'root_config' => 'config.yml'
        ), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW' => 'user'
        ));

        parent::setUp();
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
