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

 * [Allow your users to post safe HTML with ExerciseHtmlPurifierBundle](9a-markup_htmlpurifier.md)
 * [Enable the sundown pecl extension to parse comments for markdown](9b-sundown_markdown_parser.md)
 * [Implement a BBCode parser to let your users post comments with BBCode](9c-using_a_bbcode_parser.md)

## That is it!
[Return to the index.](index.md)
