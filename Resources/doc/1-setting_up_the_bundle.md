Step 1: Setting up the bundle
=============================
### A) Download and install FOSCommentBundle

To install FOSCommentBundle run the following command

``` bash
$ php composer.phar require friendsofsymfony/comment-bundle
```

### B) Enable the bundle

Finally, enable the required bundles in the kernel:

``` php
<?php
// app/AppKernel.php

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


### C) Enable method overrides.

This is strictly not needed, but if you want to be able to support editing and hiding
comments you need to edit your frontcontrollers (web/app.php and web/app_dev.php) and add a call to 
Request::enableHttpMethodParameterOverride():

``` php
<?php

Request::enableHttpMethodParameterOverride();  // <-- add this, just before:
$request = Request::createFromGlobals();

```


### Continue to the next step!
When you're done. Continue by creating the appropriate Comment and Thread classes:
[Step 2: Create your Comment and Thread classes](2-create_your_comment_and_thread_classes.md).
