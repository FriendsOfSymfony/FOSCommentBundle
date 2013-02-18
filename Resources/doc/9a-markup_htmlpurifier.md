Step 9a: Using ExerciseHTMLPurifierBundle
======================================

FOSCommentBundle allows you to use [ExerciseHTMLPurifierBundle](https://github.com/Exercise/HTMLPurifierBundle)
to sanitise HTML entered into comments.

** Note: **

> Letting users post HTML directly without appropriate safety measures can lead
> to XSS attacks. Be careful with your HTMLPurifier configuration!

FOSCommentBundle does not automatically define the parsing bridge service for
HTMLPurifier. You will need to do this in your application configuration.

Additionally, you are required to tell FOSCommentBundle about this markup class
so that it knows to use it. Both requirements are listed in the code block below

``` yaml
# app/config/config.yml

services:
    # ...
    markup.exercise_html_purifier:
        class: FOS\CommentBundle\Markup\HtmlPurifier
        arguments: [ @exercise_html_purifier.default ]
    # ...

fos_comment:
    # ...
    service:
        markup: markup.exercise_html_purifier
    # ...
```

You are able to define different configurations for HTMLPurifierBundle, just change
the argument given to the parser bridge to reflect the new HTMLPurifier configuration
you have created. More information on this can be found at [ExerciseHTMLPurifierBundle's documentation](https://github.com/Exercise/HTMLPurifierBundle)

## That is it!
[Return to the index.](index.md)
