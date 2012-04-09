Step 11: Running the test suite
======================================

FOSCommentBundle comes with both unit tests (written with PHPUnit) and functional
testing (written with Behat/Mink).

These tests have some requirements that will need to be set up before being run.

Unit Tests
--------------------------------------

Unit tests have only a few requirements

 * vendors set up by [Composer](http://getcomposer.org)
   * `php composer.phar install --install-suggests`
 * [PHPUnit](http://www.phpunit.de/manual/current/en/index.html)

Once these dependencies are installed, run the unit test suite by running `phpunit`
in the root bundle directory.

Functional Tests
--------------------------------------

Functional tests have more dependencies.

 * vendors set up by [Composer](http://getcomposer.org)
   * `php composer.phar install --install-suggests`
 * Sahi
 * A browser supported by Sahi (headless or otherwise)
 * A webserver with ability to parse PHP
 * Once vendors are installed, a few commands must be run to set up the environment
   * ./Tests/Functional/app/console assets:install --symlink Tests/Functional/web
   * ./Tests/Functional/app/console doctrine:database:drop --force
   * ./Tests/Functional/app/console doctrine:database:create
   * ./Tests/Functional/app/console doctrine:schema:create

FOSCommentBundle uses Travis-CI and provides set up in the `.travis.yml` file in
the root directory. You can base your configuration off this. There is an nginx.conf
file in `Tests/Functional/app/Behat/nginx.conf.dist` which can also be used as
a basis for configuring nginx to run functional tests.

To run Behat's functional tests, run `Tests/Functional/app/console behat`

## That is it!
[Return to the index.](index.md)
