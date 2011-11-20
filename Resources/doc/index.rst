Provides threaded comments for your Symfony2 Project.
`See it in action`_

Features
========

- Manages trees of comments
- Can include comment threads in any page
- Compatible with any persistence backend. Actually Doctrine2 mongodb-odm and ORM are implemented.
- Configurable sorting of the comment tree
- Optional use of Symfony2 Acl to protect comments
- Optional integration with FOS\UserBundle
- Optional integration with `Akismet`_

Installation
============

Add CommentBundle to your /vendor/bundles/ dir
-------------------------------------

Using the vendors script
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Add the following lines in your ``deps`` file::

    [FOSCommentBundle]
        git=https://github.com/FriendsOfSymfony/FOSCommentBundle.git
        target=bundles/FOS/CommentBundle

Run the vendors script::

    ./bin/vendors install

Using git submodules
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

::

    $ git submodule add https://github.com/FriendsOfSymfony/FOSCommentBundle.git vendor/bundles/FOS/CommentBundle

Add the FOS namespace to your autoloader
----------------------------------------

::

    // app/autoload.php

    $loader->registerNamespaces(array(
        'FOS' => __DIR__.'/../vendor/bundles',
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

Minimal configuration
---------------------

At a minimum, your configuration must define your DB driver ("orm" or "mongodb")
and the Comment and Thread classes.

We recommend that any entity that is created or used for CommentBundle uses the
DEFERRED_EXPLICIT change tracking policy.

MongoDB
~~~

The MongoDB implementation does not provide a concrete Comment class for your use,
you must create one::

    // src/MyProject/MyBundle/Document/Comment.php

    namespace MyProject\MyBundle\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use FOS\CommentBundle\Document\Comment as BaseComment;

    /**
     * @MongoDB\Document
     * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
     */
    class Comment extends BaseComment
    {
        /**
         * @MongoDB\Id
         */
        protected $id;

        /**
         * Thread of this comment
         *
         * @var Thread
         * @MongoDB\ReferenceOne(targetDocument="MyProject\MyBundle\Document\Thread")
         */
        protected $thread;

        /**
         * @return Thread
         */
        public function getThread()
        {
            return $this->thread;
        }

        /**
         * @param Thread $thread
         * @return null
         */
        public function setThread(Thread $thread)
        {
            $this->thread = $thread;
        }
    }

Additionally, create the Thread object::

    // src/MyProject/MyBundle/Document/Thread.php

    namespace MyProject\MyBundle\Document;

    use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
    use FOS\CommentBundle\Document\Thread as BaseThread;

    /**
     * @MongoDB\Document
     * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
     */
    class Thread extends BaseThread
    {

    }

Configure your application::

In YAML::

    # app/config/config.yml

    fos_comment:
        db_driver: mongodb
        class:
            model:
                comment: MyProject\MyBundle\Document\Comment
                thread: MyProject\MyBundle\Document\Thread

Or if you prefer XML::

    # app/config/config.xml

    <fos_comment:config db-driver="mongodb">
        <fos_comment:class>
            <fos_comment:model
                comment="MyProject\MyBundle\Document\Comment"
                thread="MyProject\MyBundle\Document\Thread"
            />
        </fos_comment:class>
    </fos_comment:config>

ORM
~~~

The ORM implementation does not provide a concrete Comment class for your use,
you must create one::

    // src/MyProject/MyBundle/Entity/Comment.php

    namespace MyProject\MyBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use FOS\CommentBundle\Entity\Comment as BaseComment;

    /**
     * @ORM\Entity
     * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
     */
    class Comment extends BaseComment
    {
        /**
         * @ORM\Id
         * @ORM\Column(type="integer")
         * @ORM\generatedValue(strategy="AUTO")
         */
        protected $id;

        /**
         * Thread of this comment
         *
         * @var Thread
         * @ORM\ManyToOne(targetEntity="MyProject\MyBundle\Entity\Thread")
         */
        protected $thread;

        /**
         * @return Thread
         */
        public function getThread()
        {
            return $this->thread;
        }

        /**
         * @param Thread $thread
         * @return null
         */
        public function setThread(Thread $thread)
        {
            $this->thread = $thread;
        }
    }

And the Thread::

    // src/MyProject/MyBundle/Entity/Thread.php

    namespace MyProject\MyBundle\Entity;

    use Doctrine\ORM\Mapping as ORM;
    use FOS\CommentBundle\Entity\Thread as BaseThread;

    /**
     * @ORM\Entity
     * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
     */
    class Thread extends BaseThread
    {
        /**
         * @var string $id
         *
         * @ORM\Id
         * @ORM\Column(type="string")
         */
        protected $id;
    }

Configure your application::

    # app/config/config.yml

    fos_comment:
        db_driver: orm
        class:
            model:
                comment: MyProject\MyBundle\Entity\Comment
                thread: MyProject\MyBundle\Entity\Thread

Or if you prefer XML::

    # app/config/config.xml

    <fos_comment:config db-driver="orm">
        <fos_comment:class>
            <fos_comment:model
                comment="MyProject\MyBundle\Entity\Comment"
                thread="MyProject\MyBundle\Entity\Thread"
            />
        </fos_comment:class>
    </fos_comment:config>


Register routing
----------------

You will probably want to include the builtin routes, there are only two of them:

In YAML::

    # app/config/routing.yml

    fos_comment:
        resource: "@FOSCommentBundle/Resources/config/routing.yml"

Or if you prefer XML::

    # app/config/routing.xml

    <import resource="@FOSCommentBundle/Resources/config/routing.yml"/>

Enable comments on a page
-------------------------

It is as easy as it can get. In any template, add the following::

    {% render "FOSCommentBundle:Thread:show" with {"id": "foo"} %}

The first time the page is rendered, the "foo" thread is created.

You can use any string as the identifier.

To render a list of comments with a flat strategy, use the following::

    {% render "FOSCommentBundle:Thread:showFlat" with {"id": "foo"} %}

Style it
--------

This bundle supplies some basic CSS and JS assets that will make it usable. They're
based on assets created for `lichess`_.

They have been included in the Resources/assets directory. The javascript component
requires the installation of jQuery which must be done separately to this.

Note: While the example javascript code requires jQuery this bundle does not depend on
it. You are able to rewrite the code using any javascript framework.

To use them in your templates with Assetic, place the following in your base template::

    <!-- CSS -->
    {% stylesheets '@FOSCommentBundle/Resources/assets/css/comments.css' %}
    <link rel="stylesheet" href="{{ asset_url }}" type="text/css" />
    {% endstylesheets %}

    <!-- Javascript -->
    {% javascripts '@FOSCommentBundle/Resources/assets/js/comments.js' %}
    <script type="text/javascript" src="{{ asset_url }}"></script>
    {% endjavascripts %}

The assets provided by this bundle are not intended for production use. You should
modify them to meet your own style and functionality requirements.

Integration with FOS\UserBundle
===============================

By default, comments are made anonymously.
You may want to use FOS\UserBundle authentication to sign the comments.

Override the comment class
--------------------------

Create your own Comment class to add the relation to the User model.
While there, make it implement SignedCommentInterface and VotableCommentInterface::

    // src/Bar/CommentBundle/Document/Comment.php

    <?php

    namespace Bar\CommentBundle\Document;

    use Symfony\Component\Security\Core\User\UserInterface;
    use FOS\CommentBundle\Document\Comment as BaseComment;
    use FOS\CommentBundle\Model\SignedCommentInterface;
    use FOS\CommentBundle\Model\VotableCommentInterface;
    use Bar\UserBundle\Document\User;

    /**
     * @MongoDB\Document
     * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
     */
    class Comment extends BaseComment implements SignedCommentInterface, VotableCommentInterface
    {
        /**
         * Author of the comment
         *
         * @MongoDB\ReferenceOne(targetDocument="Bar\UserBundle\Document\User")
         * @var User
         */
        protected $author;

        /**
         * @param User
         */
        public function setAuthor(UserInterface $author)
        {
            $this->author = $author;
        }

        /**
         * Get authorName
         * @return string
         */
        public function getAuthorName()
        {
            if (null === $this->getAuthor()) {
                return 'Anonymous';
            }

            return $this->getAuthor()->getUsername();
        }

        /**
         * Comment voting score.
         *
         * @MongoDB\Field(type="int")
         * @var integer
         */
        protected $score = 0;

        /**
         * Sets the current comment score.
         *
         * @param integer $score
         */
        public function setScore($score)
        {
            $this->score = intval($score);
        }

        /**
         * Increments the comment score by the provided
         * value.
         *
         * @param integer value
         * @return integer The new comment score
         */
        public function incrementScore($by = 1)
        {
            $score = $this->getScore() + intval($by);
            $this->setScore($score);
            return $score;
        }

        /**
         * Gets the current comment score.
         *
         * @return integer
         */
        public function getScore()
        {
            return $this->score;
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

Enabling use of the Symfony2 Security Component
===============================

CommentBundle comes bundled with the ability to use different security features provided
by Symfony2.

Using Symfony2's Built in Acl system
-------------------------------

To use the built in Acl system, it must first be initialised with the Symfony2 console.:

    app/console init:acl

Additionally, your configuration needs to be modified::

    # app/config/config.yml

    fos_comment:
        acl: true
        service:
            manager:
                thread: fos_comment.manager.thread.acl
                comment: fos_comment.manager.comment.acl
                vote: fos_comment.manager.vote.acl

Note: you must enable the Security Acl component::

    # app/config/security.yml

    security:
        acl:
            connection: default

Finally, you must populate the Acl system with entries that may not be there yet
by running::

    app/console fos:comment:installAces

This will make sure that the Acl entries in the database are correct. This comment
must be run whenever any configuration for security changes in FOSCommentBundle,
including enabling the security features or changing the FQCN of your extended
FOSCommentBundle objects.

Role based Acl security
--------------------------

CommentBundle also provides the ability to configure permissions based on the roles
a specific user has. See the configuration example below for how to customise the
default roles used for permissions.

To configure Role based security override the Acl services::

    # app/config/config.yml

    fos_comment:
        acl: true
        service:
            acl:
                thread: fos_comment.acl.thread.roles
                comment: fos_comment.acl.comment.roles
                vote: fos_comment.acl.vote.roles

To change the roles required for specific actions, modify the acl_roles configuration
key::

    # app/config/config.yml

    fos_comment:
        acl_roles:
            comment:
                create: IS_AUTHENTICATED_ANONYMOUSLY
                view: IS_AUTHENTICATED_ANONYMOUSLY
                edit: ROLE_ADMIN
                delete: ROLE_ADMIN
            thread:
                create: IS_AUTHENTICATED_ANONYMOUSLY
                view: IS_AUTHENTICATED_ANONYMOUSLY
                edit: ROLE_ADMIN
                delete: ROLE_ADMIN
            vote:
                create: IS_AUTHENTICATED_ANONYMOUSLY
                view: IS_AUTHENTICATED_ANONYMOUSLY
                edit: ROLE_ADMIN
                delete: ROLE_ADMIN

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

- Validate the comment against spam using the spam detection

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
  You must install `AkismetBundle`_ and set the spam_detection service in your configuration::

    fos_comment:
        service:
            spam_detection:
                comment: fos_comment.spam_detection.comment.akismet

Comment tree sorting
--------------

The default sorting algorithm will sort the tree in descending date order (newest first). CommentBundle
also provides an ascending date order sort.

To change the sorting algorithm, modify your app config::

    # app/config/config.yml

    fos_comment:
        service:
            sorting:
                default: date_asc

If you wish to implement a custom sorting algorithm, it must extend FOS\CommentBundle\Sorting\SortingInterface
and be tagged in the DIC as a fos_comment.sorter with a unique alias, which can be used in the config above::

    # app/config/services.xml

    <service id="application.sorter.custom" class="AppBundle\Sorter\Custom">
        <tag name="fos_comment.sorter" alias="custom" />
    </service>

Additionally, individual comment threads can have different sorting algorithms by specifying them in the render
tag::

    {% render "FOSCommentBundle:Thread:show" with {"id": "foo", sorter: "custom"} %}

Configuration example:
======================

All configuration options are listed below::

    # app/config/config.yml

    fos_comment:
        db_driver:    mongodb
        form:
            comment:
                name: fos_comment_comment
                type: fos_comment.comment
        class:
            model:
                comment: FOS\CommentBundle\Document\Comment
                vote: FOS\CommentBundle\Document\Vote
        acl: ~ # Enables Acl
        acl_roles: # optional configuration for the Role Acl providers.
            comment:
                create: IS_AUTHENTICATED_ANONYMOUSLY
                view: IS_AUTHENTICATED_ANONYMOUSLY
                edit: ROLE_ADMIN
                delete: ROLE_ADMIN
            thread:
                create: IS_AUTHENTICATED_ANONYMOUSLY
                view: IS_AUTHENTICATED_ANONYMOUSLY
                edit: ROLE_ADMIN
                delete: ROLE_ADMIN
            vote:
                create: IS_AUTHENTICATED_ANONYMOUSLY
                view: IS_AUTHENTICATED_ANONYMOUSLY
                edit: ROLE_ADMIN
                delete: ROLE_ADMIN
        service:
            manager:
                thread: fos_comment.manager.thread.default
                comment: fos_comment.manager.comment.default
                vote: fos_comment.manager.vote.default
            acl:
                thread: fos_comment.acl.thread.security
                comment: fos_comment.acl.comment.security
                vote: fos_comment.acl.vote.security
            form_factory:
                comment: foo_bar.form_factory.comment.default
            creator:
                comment: foo_bar.creator.comment.default
                thread: foo_bar.creator.thread.default
                vote: fos_comment.creator.vote.default
            blamer:
                comment: foo_bar.blamer.comment.noop
                vote: fos_comment.creator.vote.noop
            spam_detection:
                comment: foo_bar.spam_detection.comment.noop
            sorting:
                default: date_desc
        akismet:
            url: http://lichess.org
            api_key: keep_it_secret

Implement a new persistence backend
===================================

Manager
-------

To provide a new backend implementation:, you must implement these three interfaces:

- Model/ThreadManagerInterface.php
- Model/CommentManagerInterface.php
- Model/VoteManagerInterface.php

MongoDB manager implementation examples:

- Document/ThreadManager.php
- Document/CommentManager.php
- Document/VoteManager.php

Note that the MongoDB manager classes only contain MongoDB specific logic,
backend agnostic logic lives in the abstract managers.

Model
-----

You should also provide concrete models for the interfaces:

- Model/ThreadInterface.php
- Model/CommentInterface.php
- Model/VoteInterface.php

MongoDB model implementation examples:

- Document/Comment.php
- Document/Thread.php
- Document/Vote.php

Note that the MongoDB model classes only contain MongoDB specific logic,
backend agnostic logic lives in the abstract models.

Mapping
-------

You may also need to define mappings.

MongoDB mapping examples:

- src/FOS/CommentBundle/Resources/config/doctrine/Thread.mongodb.xml
- src/FOS/CommentBundle/Resources/config/doctrine/Comment.mongodb.xml
- src/FOS/CommentBundle/Resources/config/doctrine/Vote.mongodb.xml

.. _See it in action: http://lichess.org/1j21ti43
.. _Akismet: http://akismet.com
.. _AkismetBundle: http://github.com/ornicar/AkismetBundle
.. _lichess: http://lichess.org