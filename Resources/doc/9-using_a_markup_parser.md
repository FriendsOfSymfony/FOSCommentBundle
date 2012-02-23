Step 9: Using a markup parser
======================================

FOSComment bundle allows a developer to implement RawCommentInterface, which
will tell the bundle that your comments are to be parsed for a markup language.

Any markup language is supported, all you need is a bridging class that
implements `Markup\ParserInterface` and returns the parsed result of a comment
in raw html to be displayed on the page.

To set up your own custom markup parser, you are required to define a service
that implements the above interface, and to tell FOSCommentBundle about it,
adjust the configuration accordingly

``` yaml
# app/config/config.yml

fos_comment:
    service:
        markup: your_markup_service
```

FOSCommentBundle ships with support for Exercise\HTMLPurifierBundle and the
set up procedure for using HTMLPurifier can be found [at the following page](9a-markup_htmlpurifier.md)

This is an example [bridge implementation](9b-sundown_markdown_parser.md) that uses the PECL sundown module.

## That is it!
[Return to the index.](index.md)
