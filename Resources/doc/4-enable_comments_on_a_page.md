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

Add the following code at a desired place in the template to load the comments:

```
{% include 'FOSCommentBundle:Thread:async.html.twig' with {'id': 'foo'} %}
```

If you want have multiple threads on one page add the following code at a desired place in the template to load the comments

```
{% include 'FOSCommentBundle:Thread:async.html.twig' with {'thread_container': '.comments-box'} %}
```

Your page must have:

```
<div class="comments-box" data-thread-id="my_unique_thread_id_1"></div>
<div class="comments-box" data-thread-id="my_unique_thread_id_2"></div>
```

That's the basic setup! For additional information and configuration check the ... section and the cookbook.
