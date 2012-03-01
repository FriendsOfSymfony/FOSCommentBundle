Step 9c: Implementing a BBCode parser
======================================

There are multiple BBCode parsers available for PHP, but are generally considered
slow when implemented in pure PHP.

An example BBCode bridge is provided that uses StringParser_BBCode, available from
[GitHub](https://github.com/merk/StringParser_BBCode) which is a git mirror of the library
written by Christian Seiler located [here](http://christian-seiler.de/projekte/php/bbcode/index_en.html).

For details on the configuration, look at this [gist](https://gist.github.com/1948617). It requires
ExerciseHTMLPurifier bundle being installed but does not require the HTMLPurifier bridge to be
defined.

## That is it!
[Return to the index.](index.md)
