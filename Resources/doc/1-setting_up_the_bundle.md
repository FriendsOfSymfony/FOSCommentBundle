Step 1: Setting up the bundle
=============================
### A) Download and install FOSCommentBundle

**Note:**

> This bundle depends on the [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle). Please follow the instructions of the bundle to set it up.

To install FOSCommentBundle run the following command

``` bash
$ php composer.phar require friendsofsymfony/comment-bundle
```

### B) Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FOS\RestBundle\FOSRestBundle(),
        new FOS\CommentBundle\FOSCommentBundle(),
    );
}
```

### Continue to the next step!
When you're done. Continue by creating the appropriate Comment and Thread classes:
[Step 2: Create your Comment and Thread classes](2-create_your_comment_and_thread_classes.md).
