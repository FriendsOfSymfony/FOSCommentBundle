Step 9d: Using the PHP Markdown Extra
=====================================

The markup system in FOSCommentBundle is flexible and allows you to use any
syntax language that a parser exists for.
[PHP Markdown Extra](https://michelf.ca/projects/php-markdown/extra/) is an
extension to PHP Markdown implementing some features currently not available
with the plain Markdown syntax.

First, to install php-markdown run this command
```
        php composer.phar require michelf/php-markdown
```

You will want to create the service below in one of your application bundles.

``` php
<?php
// src/Application/CommentBundle/Markup

namespace Application\CommentBundle\Markup;

use FOS\CommentBundle\Markup\ParserInterface;
use \Michelf\MarkdownExtra;

use Symfony\Component\DependencyInjection\ContainerInterface;

class MarkdownExtraParser implements ParserInterface
{
    private $parser;

    private $purifer;

    /**
     * Constructor.
     *
     * @param \HTMLPurifier $purifier
     */
    public function __construct(\HTMLPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

    protected function getParser()
    {
        if (null === $this->parser) {
            $this->parser = new MarkdownExtra;
        }

        return $this->parser;
    }

    public function parse($raw)
    {
        // to avoid xss we must filter input
        $textPurify = $this->purifier->purify($raw);

        return $this->getParser()->defaultTransform($textPurify);
    }
}

```

And the service definition to enable this parser bridge

``` yaml
# app/config/config.yml

services:
    # ...
    markup.markdown_extra:
        class: Application\CommentBundle\Markup\MarkdownExtraParser
        arguments: ["@exercise_html_purifier.default"]
    # ...

fos_comment:
    # ...
    service:
        markup: markup.markdown_extra
    # ...
```

## That is it!
[Return to the index.](index.md)
