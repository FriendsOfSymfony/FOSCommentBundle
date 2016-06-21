Step 9: Using a markup parser
======================================

FOSComment bundle allows a developer to implement RawCommentInterface, which
will tell the bundle that your comments are to be parsed for a markup language.

You will also need to configure a rawBody field in your database to store the parsed comments.

```php
use FOS\CommentBundle\Model\RawCommentInterface;

class Comment extends BaseComment implements RawCommentInterface
{
    /**
     * @ORM\Column(name="rawBody", type="text", nullable=true)
     * @var string
     */
    protected $rawBody;

    ... also add getter and setter as defined in the RawCommentInterface ...
}
```

When a comment is added, it is parsed and setRawBody() is called with the raw version
of the comment which is then stored in the database and shown when the comment is later rendered.

Any markup language is supported, all you need is a bridging class that
implements `Markup\ParserInterface` and returns the parsed result of a comment
in raw html to be displayed on the page.

To set up your own custom markup parser, you are required to define a service
that implements the above interface, and to tell FOSCommentBundle about it,
adjust the configuration accordingly.

``` yaml
# app/config/config.yml

fos_comment:
    service:
        markup: your_markup_service
```

 * [Allow your users to post safe HTML with ExerciseHtmlPurifierBundle](9a-markup_htmlpurifier.md)
 * [Enable the sundown pecl extension to parse comments for markdown](9b-sundown_markdown_parser.md)
 * [Implement a BBCode parser to let your users post comments with BBCode](9c-using_a_bbcode_parser.md)
 * [Implement the PHP Markdown extra parser](9d-php_markdown_extra_parser.md)

## That is it!
[Return to the index.](index.md)
