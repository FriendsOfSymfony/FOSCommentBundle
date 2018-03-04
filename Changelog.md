Changelog
=========

### 2.1.0 (2018-03-04)

* Dropped PHP < 5.6 support.
* Dropped Symfony < 2.7 support.
* Dropped jQuery < 3 support.
* Dropped HHVM support.
* Added Symfony 4 support.
* Signature of `ThreadPermalinkListener` has been changed.
* Route `ThreadController::getThreadsActions` throws 404 if called without id's.
* Added pipeline parser to integrate several parsers together.
* Added flat view for async thread.
* Removed HTTP class constants in `ThreadController`.
* Fixed form disappearing when creating a new reply.
* Fixed failing validator with encoded permalink.
