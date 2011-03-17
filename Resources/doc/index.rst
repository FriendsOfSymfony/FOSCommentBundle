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

Integration with FOS\UserBundle
===============================

By default, comments are made anonymously.
You may want to use FOS\UserBundle authentication to sign the comments.

Override the comment class
--------------------------

Create your own Comment class to add the relation to the User model.
While there, make it implement SignedCommentInterface::

    // src/Bar/CommentBundle/Document/Comment.php

    <?php

    namespace Bar\CommentBundle\Document;

    use FOS\CommentBundle\Document\Comment as BaseComment;
    use FOS\CommentBundle\Model\SignedCommentInterface;
    use Bar\UserBundle\Document\User;

    /**
     * @mongodb:Document()
     */
    class Comment extends BaseComment implements SignedCommentInterface
    {
        /**
         * Author of the comment
         *
         * @mongodb:ReferenceOne(targetDocument="Bar\UserBundle\Document\User")
         * @var User
         */
        protected $author;

        /**
         * @param User
         */
        public function setAuthor($author)
        {
            $this->author = $author;
        }

        /**
         * Get authorName
         * @return string
         */
        public function getAuthorName()
        {
            return $this->getAuthor()->getUsername();
        }
    }

Then declare your comment class::        

    # app/config/config.yml

    fos_comment:
        db_driver:    mongodb
        class:
            model:
                comment: Bar\CommentBundle\Document\Comment

Use the builtin security blamer
-------------------------------

Now tell CommentBundle to use the authenticated FOS User to sign new comments::

    # app/config/config.yml

    fos_comment:
        service:
            blamer:
                comment: fos_comment.blamer.comment.security
    
And that's it, really.

Notable services
================

You can replace the following services with your own implementation:

Blamer
------

Blamer/CommentBlamerInterface.php::

    interface CommentBlamerInterface
    {
        function blame(CommentInterface $comment);
    }


The blamer service is responsible for giving an author name to a new comment.

CommentBundle provides two implementations:

- fos_comment.blamer.comment.noop

  This one does nothing. Comments are anonymous.
  It is the default blamer implementation.

- fos_comment.blamer.comment.security 

  Uses Symfony2 Security token user to sign comments.
  Expects comments implementing SignedCommentInterface.

You can change the blamer implementation from your app config::

    # app/config/config.yml

    fos_comment:
        service:
            blamer:
                comment: bar_comment.my_comment_blamer

Creator
-------

Creator/CommentCreatorInterface.php::

    interface CommentCreatorInterface
    {
        function create(CommentInterface $comment);
    }

Responsible for creating new comments from a request.

The default implementation does the following things to create a comment:

- Sign the comment using the comment blamer

- Validate the comment against spam if akismet is enabled

- Choose a parent comment, if the request provides one
  If no parent is given, the new comment will be added to the tree root.

- Save the comment using the comment manager

You can change the creator implementation from your app config::

    # app/config/config.yml

    fos_comment:
        service:
            creator:
                comment: bar_comment.my_comment_creator

Spam detection
--------------

SpamDetection/SpamDetectionInterface.php::

    interface SpamDetectionInterface
    {
        function isSpam(CommentInterface $comment);
    }

Decides if a comment is a spam or not.

CommentBundle ships with two implementations:

- fos_comment.spam_detection.comment.noop

  This one does nothing. Comments are never considered as spam.
  It is the default spam_detection implementation.

- fos_comment.spam_detection.comment.akismet 

  Uses `Akismet`_ to check comments against spam.
  Requires two configuration values from your app config::

    fos_comment:
        service:
            spam_detection:
                comment: bar_comment.spam_detection.comment.akismet
        akismet:
            url: http://website.com/
            api_key: keep_it_secret

You can change the blamer implementation from your app config::

    # app/config/config.yml

    fos_comment:
        service:
            blamer:
                comment: bar_comment.my_comment_spam_detection

Configuration example:
======================

All configuration options are listed below::

    # app/config/config.yml

    fos_comment:
        db_driver:    mongodb
        class:
            model:
                comment: FOS\CommentBundle\Document\Comment
            form:
                comment: FOS\CommentBundle\Document\CommentForm
        service:
            form_factory:
                comment: foo_bar.form_factory.comment.default
            creator:
                comment: foo_bar.creator.comment.default
                thread: foo_bar.creator.thread.default
            blamer:
                comment: foo_bar.blamer.comment.noop
            spam_detection:
                comment: foo_bar.spam_detection.comment.noop
        akismet:
            url: http://lichess.org
            api_key: keep_it_secret

.. _See it in action: http://lichess.org/1j21ti43
.. _Akismet: http://akismet.com
.. _CSS: https://github.com/ornicar/lichess/blob/master/src/Application/CommentBundle/Resources/public/css/comment.css
.. _JS: https://github.com/ornicar/lichess/blob/master/src/Application/CommentBundle/Resources/public/js/form.js

Implement a new persistence backend
===================================

Manager
-------

To provide a new backend implementation:, you must implement these two interfaces:

- Model/ThreadManagerInterface.php
- Model/CommentManagerInterface.php 

MongoDB manager implementation examples:

- Document/ThreadManager.php
- Document/CommentManager.php 

Note that the MongoDB manager classes only contain MongoDB specific logic,
backend agnostic logic lives in the abstract managers.

Model
-----

You should also provide concrete models for the interfaces:

- Model/ThreadInterface.php
- Model/CommentInterface.php 

MongoDB model implementation examples:

- Document/Comment.php 
- Document/Thread.php

Note that the MongoDB model classes only contain MongoDB specific logic,
backend agnostic logic lives in the abstract models.

Mapping
-------

You may also need to define mappings.

MongoDB mapping examples:

- src/FOS/CommentBundle/Resources/config/doctrine/metadata/mongodb/FOS.CommentBundle.Document.Thread.dcm.xml
- src/FOS/CommentBundle/Resources/config/doctrine/metadata/mongodb/FOS.CommentBundle.Document.Comment.dcm.xml
