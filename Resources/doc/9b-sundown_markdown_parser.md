Step 9b: Using the Sundown PECL extension
=========================================

The markup system in FOSCommentBundle is flexible and allows you to use any
syntax language that a parser exists for. PECL has an extension for markdown
parsing called Sundown, which is faster than pure PHP implementations of a
markdown parser.

First, you will need to use PECL to install Sundown. `pecl install sundown`.

You will want to create the service below in one of your application bundles.

``` php
<?php
// src/Application/CommentBundle/Markup/Sundown.php

namespace Application\CommentBundle\Markup;

use FOS\CommentBundle\Markup\ParserInterface;
use Sundown\Markdown;

class Sundown implements ParserInterface
{
    private $parser;

    protected function getParser()
    {
        if (null === $this->parser) {
            $this->parser = new Markdown(
                new \Sundown\Render\HTML(array('filter_html' => true)),
                array('autolink' => true)
            );
        }

        return $this->parser;
    }

    public function parse($raw)
    {
        return $this->getParser()->render($raw);
    }
}
```

And the service definition to enable this parser bridge

``` yaml
# app/config/config.yml

services:
    # ...
    markup.sundown_markdown:
        class: Application\CommentBundle\Markup\Sundown
    # ...

fos_comment:
    # ...
    service:
        markup: markup.sundown_markdown
    # ...
```

## That is it!
[Return to the index.](index.md)
