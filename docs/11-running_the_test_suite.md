Step 11: Running the test suite
===============================

FOSCommentBundle comes with both unit and functional tests written using PHPUnit.

When contributing to FOSCommentBundle, please provide test coverage for your
change and make sure the existing test suite passes before submitting a pull
request.

Unit and Functional Tests
-------------------------

Unit and functional tests both use PHPUnit which has a few requirements to run:

 * vendors set up by [Composer](http://getcomposer.org)
   * `php composer.phar install --dev`
 * A [PHPUnit](http://www.phpunit.de/manual/current/en/index.html) installation

Once these dependencies are installed, run the unit test suite by running `phpunit`
in the root bundle directory.

PHPUnit will use phpunit.xml.dist provided by `FOSCommentBundle`. You can
customise the test run by copying `phpunit.xml.dist` to `phpunit.xml` and making
your modifications.

Travis CI
---------

FOSCommentBundle uses Travis-CI and provides set up in the `.travis.yml` file. You
can enable Travis CI on your fork to get build notifications on any branch you
create for a pull request.


## That is it!
[Return to the index.](index.md)
