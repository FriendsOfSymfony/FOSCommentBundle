Step 5: Style it
================
This bundle supplies some basic CSS markup that will make it usable. It's
included in the `Resources/assets/css` directory.

To use the basic CSS in your templates with Assetic, place the following in your base template::

``` html
<!-- CSS -->
{% stylesheets '@FOSCommentBundle/Resources/assets/css/comments.css' %}
<link rel="stylesheet" href="{{ asset_url }}" type="text/css" />
{% endstylesheets %}
```

## That is it!
[Return to the index.](index.md)
