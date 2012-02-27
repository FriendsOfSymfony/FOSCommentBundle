<?php

namespace FOS\CommentBundle\Tests\Functional;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    private $config;

    public function __construct()
    {
        parent::__construct('test', true);

        $config = 'default.yml';

        $fs = new Filesystem();
        if (!$fs->isAbsolutePath($config)) {
            $config = __DIR__.'/config/'.$config;
        }

        if (!file_exists($config)) {
            throw new \RuntimeException(sprintf('The config file "%s" does not exist.', $config));
        }

        $this->config = $config;
    }

    public function registerBundles()
    {
        return array(
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\TwigBundle\TwigBundle(),
            new \Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new \Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new \Symfony\Bundle\AsseticBundle\AsseticBundle(),

            new \FOS\RestBundle\FOSRestBundle(),
            new \FOS\UserBundle\FOSUserBundle(),
            new \FOS\CommentBundle\FOSCommentBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle($this),

            new \FOS\CommentBundle\Tests\Functional\Bundle\CommentBundle\CommentBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->config);
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir().'/FOSCommentBundle';
    }
}