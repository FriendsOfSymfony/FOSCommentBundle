2012-01-21

* Blamers, Creators and Spam Detection classes have been moved to an Event Dispatcher based set up. Documentation on how to use this feature is expected to be available with the release of v1.0.0
* CommentManager, ThreadManager and VoteManager's interfaces have changed slightly, renaming add*() methods to save*().

2011-08-10

* ORM: Column names like ``createdAt`` have been changed to underscore delimited format ``created_at``. Schema update and cache clearance is required for migration.

2011-08-08

* Thread property ``identifier`` has been renamed to ``id``
* ORM: Comment property ancestors has been marked as not null and should default to an empty string

2011-07-31

* The supplied Thread classes are now mapped-superclasses, you must extend and implement an appropriate class from Entity/ or Document/ for your application and adjust your configuration accordingly.
