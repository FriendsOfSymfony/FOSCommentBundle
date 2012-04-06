<?php

namespace FOS\CommentBundle\Tests\Functional;

use Behat\MinkBundle\Driver\SymfonyDriver as BaseSymfonyDriver;
use Symfony\Component\BrowserKit\Client;

class SymfonyDriver extends BaseSymfonyDriver
{
    public function __construct(Client $client = null)
    {
        $class  = get_class($client->getKernel());
        $kernel = new $class('Behat', 'config.yml', $client->getKernel()->getEnvironment(), $client->getKernel()->isDebug());
        $kernel->boot();

        $this->client = $kernel->getContainer()->get('test.client');
        $this->client->followRedirects(true);
    }
}