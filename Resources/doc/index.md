Getting Started With FOSCommentBundle
=====================================

## Installation

Installation is a quick (I promise!) x step process:

1. Download FOSCommentBundle
2. Configure the Autoloader
3. Enable the Bundle
4. Create your Comment and Thread classes
5. Import FOSCommentBundle routing
6. Enable comments on a page


### Step 1: Download FOSCommentBundle

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

### Step 2: Configure the Autoloader

Add the `FOS` namespace to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
    'FOS' => __DIR__.'/../vendor/bundles',
));
```

### Step 3: Enable the bundle

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

### Step 4: Create your Comment and Thread classes

The FOSCommentBundle supports both Doctrine ODM (mongodb) and Doctrine ORM by
default. However, you must provide a concrete Comment and Thread class. Follow
the appropriate instructions to set up the classes:
- [Doctrine ORM](mapping_orm.md)
- [Doctrine ODM (mongodb)](mapping_mongodb.md)

After the classes are created and configured you can continue with step 5.

### Step 5: Import FOSCommentBundle routing

Import the bundle routing:

``` yaml
fos_comment_api:
    type: rest
    resource: "@FOSCommentBundle/Resources/config/routing.yml"
    prefix: /api
```
**Note:**

> The `type: rest` part is important.

### Step 6: Enable comments on a page
The recommended way to include comments on a page is using the reference
javascript provided. The javascript will asynchronously load the comments after
the page load.

At the place where the comments should be loaded, add this to the html:
``` html
<div id="fos_comment_thread"></div>
```

And the following code to actually load the comments:
``` jinja
{% include 'FOSCommentBundle:Thread:async.html.twig' with {'id': 'foo'} %}
```

That's the basic setup! For additional information and configuration check the ... section and the cookbook.
