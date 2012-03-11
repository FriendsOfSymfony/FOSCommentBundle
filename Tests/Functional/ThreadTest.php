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

use Doctrine\ORM\Tools\SchemaTool;

/**
 * Basic functional testing of usual methods used to include a
 * comment thread inside another page.
 *
 * @author Tim Nagel <tim@nagel.com.au>
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
