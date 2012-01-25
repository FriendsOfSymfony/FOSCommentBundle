Step 1: Setting up the bundle
=============================
### A) Download FOSCommentBundle

**Note:**

> This bundle depends on the [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle). Please follow the instructions of the bundle to set it up.

Ultimately, the FOSCommentBundle files should be downloaded to the
`vendor/bundles/FOS/CommentBundle` directory.

This can be done in several ways, depending on your preference. The first
method is the standard Symfony2 method.

**Using the vendors script**

Add the following lines in your `deps` file:

```
[FOSCommentBundle]
    git=https://github.com/FriendsOfSymfony/FOSCommentBundle.git
    target=bundles/FOS/CommentBundle
```

Now, run the vendors script to download the bundle:

``` bash
$ php bin/vendors install
```

**Using submodules**

If you prefer instead to use git submodules, then run the following:

``` bash
$ git submodule add git://github.com/FriendsOfSymfony/FOSCommentBundle.git vendor/bundles/FOS/CommentBundle
$ git submodule update --init
```

### B) Configure the Autoloader

Add the `FOS` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'FOS' => __DIR__.'/../vendor/bundles',
));
```

### C) Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FOS\CommentBundle\FOSCommentBundle(),
    );
}
```

### Continue to the next step!
When you're done. Continue by creating the appropriate Comment and Thread classes:
[Step 2: Create your Comment and Thread classes](2-create_your_comment_and_thread_classes.md).
