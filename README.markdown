FOSCommentBundle
=============

The FOSCommentBundle adds support for a comment system in Symfony2. Features include:

- Manages trees of comments
- Can include comment threads in any page
- Compatible with any persistence backend. Doctrine2 mongodb-odm and ORM are implemented.
- Configurable sorting of the comment tree
- REST api
- Extensible through events fired during the comment lifecycle
- Optional use of Symfony2 Acl to protect comments
- Optional integration with FOS\UserBundle
- Optional integration with `Akismet`_
- Optional markup parser support (eg HtmlPurifier or php-sundown)

**Note:**

> The structure of this bundle has changed recently. If you are using 0.9.x, there are changes you need to make listed in UPDATE.markdown. There is also a 0.9 branch if you do not wish to upgrade, however this branch is not supported.

[![Build Status](https://secure.travis-ci.org/FriendsOfSymfony/FOSCommentBundle.png?branch=master)](http://travis-ci.org/FriendsOfSymfony/FOSCommentBundle)

Documentation
-------------

The bulk of the documentation is stored in the `Resources/doc/index.md`
file in this bundle:

[Read the Documentation](https://github.com/FriendsOfSymfony/FOSCommentBundle/blob/master/Resources/doc/index.md)

Installation
------------

All the installation instructions are located in [documentation](https://github.com/FriendsOfSymfony/FOSCommentBundle/blob/master/Resources/doc/index.md).

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE
