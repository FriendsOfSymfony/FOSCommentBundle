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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

/**
 * Base Functional test case. Inspired (copied) from FrameworkBundle and SecurityBundle's
 * functional test suites.
 */
class WebTestCase extends BaseWebTestCase
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $em;
    protected static $schemaSetUp = false;

    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    public static function assertRedirect($response, $location)
    {
        self::assertTrue($response->isRedirect(), 'Response should be a redirect, got status code: '.substr($response, 0, 2000));
        self::assertEquals('http://localhost'.$location, $response->headers->get('Location'));
    }

    protected function setUp()
    {
        if (!class_exists('Twig_Environment')) {
            $this->markTestSkipped('Twig is not available.');
        }

        if (null === $this->em) {
            $this->em = $this->client->getContainer()->get('doctrine')->getManager();

            if (!static::$schemaSetUp) {
                $st = new SchemaTool($this->em);

                $classes = $this->em->getMetadataFactory()->getAllMetadata();
                $st->dropSchema($classes);
                $st->createSchema($classes);

                static::$schemaSetUp = true;
            }
        }

        parent::setUp();
    }

    protected function deleteTmpDir($testCase)
    {
        if (!file_exists($dir = sys_get_temp_dir().'/'.Kernel::VERSION.'/'.$testCase)) {
            return;
        }

        $fs = new Filesystem();
        $fs->remove($dir);
    }

    protected static function getKernelClass()
    {
        require_once __DIR__.'/app/AppKernel.php';

        return 'FOS\\CommentBundle\\Tests\\Functional\\AppKernel';
    }

    protected static function createKernel(array $options = array())
    {
        $class = self::getKernelClass();

        if (!isset($options['test_case'])) {
            throw new \InvalidArgumentException('The option "test_case" must be set.');
        }

        return new $class(
            $options['test_case'],
            isset($options['root_config']) ? $options['root_config'] : 'config.yml',
            isset($options['environment']) ? $options['environment'] : 'foscommenttest',
            isset($options['debug']) ? $options['debug'] : true
        );
    }
}
