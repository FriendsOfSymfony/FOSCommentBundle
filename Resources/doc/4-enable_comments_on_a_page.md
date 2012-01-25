Step 4: Enable comments on a page
=================================
The recommended way to include comments on a page is using the reference
javascript provided. The javascript will asynchronously load the comments after
the page load.

And the following code at a desired place in the template to load the comments:

```
{% include 'FOSCommentBundle:Thread:async.html.twig' with {'id': 'foo'} %}
```

That's the basic setup! For additional information and configuration check the ... section and the cookbook.
