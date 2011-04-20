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

ORM
~~~

The ORM implementation does not provide a concrete Comment class for your use,
you must create one::

    // src/MyProject/MyBundle/Entity/Comment.php

    namespace MyProject\MyBundle\Entity;
    use FOS\CommentBundle\Entity\Comment as BaseComment;

    /**
     * @orm:Entity
     */
    class Comment extends BaseComment
    {
        /**
         * @orm:Id
         * @orm:Column(type="integer")
         * @orm:generatedValue(strategy="AUTO")
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

Or if you prefer XML::

    # app/config/config.xml

    <fos_comment:config db-driver="orm">
        <fos_comment:class>
            <fos_comment:model
                comment="MyProject\MyBundle\Entity\Comment"
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

    {% render "FOSCommentBundle:Thread:show" with {"identifier": "foo"} %}

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
While there, make it implement SignedCommentInterface and VotableCommentInterface::

    // src/Bar/CommentBundle/Document/Comment.php

    <?php

    namespace Bar\CommentBundle\Document;

    use FOS\CommentBundle\Document\Comment as BaseComment;
    use FOS\CommentBundle\Model\SignedCommentInterface;
    use FOS\CommentBundle\Model\VotableCommentInterface;
    use Bar\UserBundle\Document\User;

    /**
     * @mongodb:Document()
     */
    class Comment extends BaseComment implements SignedCommentInterface, VotableCommentInterface
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

        /**
         * Comment voting score.
         *
         * @mongodb:Field(type="int")
         * @var integer
         */
        protected $score;

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

CommentBundle comes bundled with the ability to use Acl to protect components. To
use this feature, it must be enabled in the configuration::

    # app/config/config.yml

    fos_comment:
        service:
            manager:
                thread: fos_comment.manager.thread.acl
                comment: fos_comment.manager.comment.acl
                vote: fos_comment.manager.vote.acl

Note: you must enable the Security Acl component::

    # app/config/config.yml

    security:
        acl:
            connection: default

Populating the Acl component
--------------------------

When enabling the Acl setting you must run the fos:comment:installAces command to
make sure that all Comments and Threads have appropriate Acl entries.

This command must also be run if you turn Acl off and re-enable it at a later date
or change the FQCN of the Comment object.

Role based Acl security
--------------------------

CommentBundle also provides the ability to configure permissions based on the roles
a specific user has. See the configuration example below for how to customise the
default roles used for permissions.

To configure Role based security override the Acl services::

    # app/config/config.yml

    fos_comment:
        service:
            acl:
                thread: fos_comment.acl.thread.roles
                comment: fos_comment.acl.comment.roles
                vote: fos_comment.acl.vote.roles

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
  Requires two configuration values from your app config::

    fos_comment:
        service:
            spam_detection:
                comment: fos_comment.spam_detection.comment.akismet
        akismet:
            url: http://website.com/
            api_key: keep_it_secret

You can change the blamer implementation from your app config::

    # app/config/config.yml

    fos_comment:
        service:
            blamer:
                comment: bar_comment.my_comment_spam_detection

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

    {% render "FOSCommentBundle:Thread:show" with {"identifier": "foo", sorter: "custom"} %}

Configuration example:
======================

All configuration options are listed below::

    # app/config/config.yml

    fos_comment:
        db_driver:    mongodb
        class:
            model:
                comment: FOS\CommentBundle\Document\Comment
                vote: FOS\CommentBundle\Document\Vote
            form:
                comment: FOS\CommentBundle\Document\CommentForm
        acl:
            roles: # optional configuration for the Role Acl providers.
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

- src/FOS/CommentBundle/Resources/config/doctrine/metadata/mongodb/FOS.CommentBundle.Document.Thread.dcm.xml
- src/FOS/CommentBundle/Resources/config/doctrine/metadata/mongodb/FOS.CommentBundle.Document.Comment.dcm.xml
- src/FOS/CommentBundle/Resources/config/doctrine/metadata/mongodb/FOS.CommentBundle.Document.Vote.dcm.xml

.. _See it in action: http://lichess.org/1j21ti43
.. _Akismet: http://akismet.com
.. _CSS: https://github.com/ornicar/lichess/blob/master/src/Application/CommentBundle/Resources/public/css/comment.css
.. _JS: https://github.com/ornicar/lichess/blob/master/src/Application/CommentBundle/Resources/public/js/form.js
