2011-08-08

* Thread property ``identifier`` has been renamed to ``id``
* ORM: Comment property ancestors has been marked as not null and should default to an empty string

2011-07-31

* The supplied Thread classes are now mapped-superclasses, you must extend and implement an appropriate class from Entity/ or Document/ for your application and adjust your configuration accordingly.