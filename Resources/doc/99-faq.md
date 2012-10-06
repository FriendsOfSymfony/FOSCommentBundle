Frequently Asked Questions
=====================

###  After including the async template I can't see anything new on my page. Do I need to follow some other steps?
No, there is no additional steps required for a basic configuration. Please check the following:

* Check the HTML output of your page and ensure there is some javascript code included.
* Ensure there are no javascript errors on your page
* Check the HTTP requests. Should have the following:
 * An initial request to your page
 * A request to the jQuery library
 * A request to a javascript file. Something like /web/js/35a8e64.js
 * A request to the FOSCommentBundle's API. Something like /web/app.php/api/threads/test

###  How to solve the error "Fatal error: Maximum function nesting level of '100' reached"?

This error only occurs when xdebug is installed and is common with the default maximum of 100 (without xdebug, there is no cap).

```php
xdebug.max_nesting_level = 200
```

in your php.ini will fix it up
