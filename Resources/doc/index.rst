Provides threaded comments for your Symfony2 Project.
`See it in action`_

Features
========

- Manages trees of comments
- Can include comment threads in any page
- Compatible with any persistence backend. Actually Doctrine2 mongodb-odm is implemented.
- Optional integration with FOS\UserBundle
- Optional integration with `Akismet`_

Installation
============

Add CommentBundle to your src/ dir
-------------------------------------

::

    $ git submodule add git://github.com/FriendsOfSymfony/CommentBundle.git src/FOS/CommentBundle

Add the FOS namespace to your autoloader
----------------------------------------

::

    // app/autoload.php
    $loader->registerNamespaces(array(
        'FOS' => __DIR__.'/../src',
        // your other namespaces
    );

Add CommentBundle to your application kernel
-----------------------------------------

::

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new FOS\CommentBundle\FOSCommentBundle(),
            // ...
        );
    }

Configure your project
----------------------

You have to include the CommentBundle in your Doctrine mapping configuration,
along with the bundle containing your custom Comment class::

    # app/config/config.yml
    doctrine_mongo_db:
        document_managers:
            default:
                mappings:
                    FOSCommentBundle: ~
                    # your other bundles

The above example assumes a MongoDB configuration, but the `mappings` configuration
block would be the same for ORM.

Minimal configuration
---------------------

At a minimum, your configuration must define your DB driver ("orm" or "mongodb")
and a Comment class.

ODM
~~~

In YAML::

    # app/config/config.yml
    fos_comment:
        db_driver: mongodb
        class:
            model:
                comment: FOS\CommentBundle\Document\Comment

Or if you prefer XML::

    # app/config/config.xml

    <fos_comment:config db-driver="mongodb">
        <fos_comment:class>
            <fos_comment:model
                comment="FOS\CommentBundle\Document\Comment"
            />
        </fos_comment:class>
    </fos_comment:config>


Register routing
----------------

You will probably want to include the builtin routes, there are only two of them:

In YAML::

    # app/config/routing.yml
    fos_comment:
        resource: @FOSCommentBundle/Resources/config/routing.yml

Or if you prefer XML::

    # app/config/routing.xml

    <import resource="@FOSCommentBundle/Resources/config/routing.yml"/>

Enable comments on a page
-------------------------

It is as easy as it can get. In any template, add the following::

    {% render "FOSComment:Thread:show" with {"identifier": "foo"} %}

The first time the page is rendered, the "foo" thread is created.

You can use any string as the identifier.

Style it
--------

Nested comments require CSS and JS to be usable.
Such assets are not provided in this bundle,
but you can take inspiration from these ones: `CSS`_ `JS`_

Configuration example:
======================

All configuration options are listed below::

    fos_comment:
        db_driver:    mongodb
        class:
            model:
                comment: Foo\BarBundle\Document\Comment
            form:
                comment: Foo\BarBundle\Document\CommentForm
        service:
            form_factory:
                comment: foo_bar.form_factory.comment
            creator:
                comment: foo_bar.creator.comment
                thread: foo_bar.creator.thread
            blamer:
                comment: foo_bar.blamer.comment
        akismet:
            enabled: true
            url: http://lichess.org
            api_key: keep_it_secret

.. _See it in action: http://lichess.org/1j21ti43
.. _Akismet: http://akismet.com
.. _CSS: https://github.com/ornicar/lichess/blob/master/src/Application/CommentBundle/Resources/public/css/comment.css
.. _JS: https://github.com/ornicar/lichess/blob/master/src/Application/CommentBundle/Resources/public/js/form.js
