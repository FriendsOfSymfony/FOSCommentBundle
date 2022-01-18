Step 1: Setting up the bundle
=============================
### A) Download and install FOSCommentBundle

To install FOSCommentBundle run the following command

``` bash
$ php composer.phar require friendsofsymfony/comment-bundle
```

### B) Enable the bundle

Enable the required bundles in the kernel:

``` php
<?php
// app/AppKernel.php OR config/bundles.php for Symfony 4.x

public function registerBundles()
{
    $bundles = array(
        // ...
        new FOS\RestBundle\FOSRestBundle(),
        new FOS\CommentBundle\FOSCommentBundle(),
        new JMS\SerializerBundle\JMSSerializerBundle($this),
    );
}
```

### C) Enable HTTP Method Override

[Enable HTTP Method override as described here](https://symfony.com/doc/current/reference/configuration/framework.html#http-method-override)

``` yaml
# app/config/config.yml

framework:
    http_method_override: true
```

Or


``` php
// public/index.php

$kernel = new CacheKernel($kernel);

Request::enableHttpMethodParameterOverride(); // <-- add this line
$request = Request::createFromGlobals();
```

### D) Enable translations

If you wish to use default texts provided in this bundle, you have to make
sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: ~
```

For more information about translations, check [Symfony documentation](http://symfony.com/doc/current/book/translation.html).

### Continue to the next step!
When you're done. Continue by creating the appropriate Comment and Thread classes:
[Step 2: Create your Comment and Thread classes](2-create_your_comment_and_thread_classes.md).
