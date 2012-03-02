<?php

namespace FOS\CommentBundle\Tests\Functional;

use Doctrine\ORM\Tools\SchemaTool;

class ThreadTest extends BaseTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    public function setUp()
    {
        $this->client = self::createClient();

        /** @var \Doctrine\ORM\EntityManager $em  */
        $em = $this->client->getContainer()->get('doctrine')->getEntityManager();
        $st = new \Doctrine\ORM\Tools\SchemaTool($em);

        $classes = $em->getMetadataFactory()->getAllMetadata();
        $st->dropSchema($classes);
        $st->createSchema($classes);
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
