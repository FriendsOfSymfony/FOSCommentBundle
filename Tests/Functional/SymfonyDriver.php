<?php

namespace FOS\CommentBundle\Tests\Functional;

use Behat\Mink\Driver\GoutteDriver;
use Symfony\Component\BrowserKit\Client;

/*
 * This file has been duplicated from Behat, a private $client
 * does not allow us to overwrite the "new $class()" call because
 * our kernel has different parameters to the normal Kernel.
 */

/**
 * Symfony2 Mink driver.
 *
 * @author      Konstantin Kudryashov <ever.zet@gmail.com>
 */
class SymfonyDriver extends GoutteDriver
{
    /**
     * Initializes Goutte driver.
     *
     * @param   Symfony\Component\BrowserKit\Client $client     BrowserKit client instance
     */
    public function __construct(Client $client = null)
    {
        // create new kernel, that could be easily rebooted
        $class  = get_class($client->getKernel());
        $kernel = new $class('Behat', 'config.yml', $client->getKernel()->getEnvironment(), $client->getKernel()->isDebug());
        $kernel->boot();

        parent::__construct($kernel->getContainer()->get('test.client'));
    }

    /**
     * {@inheritdoc}
     *
     * removes "*.php/" from urls and then passes it to GoutteDriver::visit().
     */
    public function visit($url)
    {
        $url = preg_replace('/^(https?\:\/\/[^\/]+)(\/[^\/]+\.php)?/', '$1', $url);
        parent::visit($url);
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        parent::reset();

        $this->getClient()->getKernel()->shutdown();
        $this->getClient()->getKernel()->boot();
    }

    /**
     * {@inheritdoc}
     */
    public function setBasicAuth($user, $password)
    {
        $this->getClient()->setServerParameter('PHP_AUTH_USER', $user);
        $this->getClient()->setServerParameter('PHP_AUTH_PW', $password);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestHeader($name, $value)
    {
        switch (strtolower($name)) {
            case 'accept':
                $name = 'HTTP_ACCEPT';
                break;
            case 'accept-charset':
                $name = 'HTTP_ACCEPT_CHARSET';
                break;
            case 'accept-encoding':
                $name = 'HTTP_ACCEPT_ENCODING';
                break;
            case 'accept-language':
                $name = 'HTTP_ACCEPT_LANGUAGE';
                break;
            case 'connection':
                $name = 'HTTP_CONNECTION';
                break;
            case 'host':
                $name = 'HTTP_HOST';
                break;
            case 'user-agent':
                $name = 'HTTP_USER_AGENT';
                break;
            case 'authorization':
                $name = 'PHP_AUTH_DIGEST';
                break;
        }

        $this->getClient()->setServerParameter($name, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function getResponseHeaders()
    {
        $headers         = array();
        $responseHeaders = trim($this->getClient()->getResponse()->headers->__toString());

        foreach (explode("\r\n", $responseHeaders) as $header) {
            list($name, $value) = array_map('trim', explode(':', $header, 2));

            if (isset($headers[$name])) {
                $headers[$name]   = array($headers[$name]);
                $headers[$name][] = $value;
            } else {
                $headers[$name] = $value;
            }
        }

        return $headers;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return $this->getClient()->getResponse()->getStatusCode();
    }
}
