Changelog
=========

### 3.0.0 (2017-xx-xx)

* Dropped PHP < 5.6 support.
* Dropped Symfony < 2.7 support.
* Dropped jQuery < 3 support.
* Dropped HHVM support.
* Added Symfony 4 support.
* Signature of `ThreadPermalinkListener` has been changed.
* Decrease `Thread::numComments` after comment removal.
* Route `ThreadController::getThreadsActions` throws 404 if called without id's.
* Added pipeline parser to integrate several parsers together.
* Added flat view for async thread.
* Removed HTTP class constants in `ThreadController`.
* Fixed form disappearing when creating a new reply.
