Step 4: Enable comments on a page
=================================
The recommended way to include comments on a page is using the reference
javascript provided. The javascript will asynchronously load the comments after
the page load.

> **Note:**
> The implementation javascript provided with FOSCommentBundle relies on jQuery 1.7
> You will need to install this separately and make sure that it is available on the
> page you want to enable comments on.
>
> You are welcome to rewrite the reference implementation using another javascript
> framework.

And the following code at a desired place in the template to load the comments:

```
{% include 'FOSCommentBundle:Thread:async.html.twig' with {'id': 'foo'} %}
```

Or if you want to load the flat view:

```
{% include 'FOSCommentBundle:Thread:async.html.twig' with {'id': 'foo', 'view': 'flat'} %}
```

## That is it!
That's the basic setup! [Return to the index.](index.md)
