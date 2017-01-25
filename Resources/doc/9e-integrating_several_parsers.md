Step 9e: Integrating several parsers together
=============================================

Purpose
--------
Imagine that you need to integrate ExerciseHTMLPurifierBundle parser, bbcode parser, any other parser... it's better to have several parsers where each is responsible for it's action. That's why it's better to make chain of parsers, where order is important and parsers works one after other.

Usage
-----

First, configure parser classes as services. You don't have to use all parsers that this bundle provides. The main parser of this bundle is PipelineParser which provides pipeline for parser classes.

```yaml
    # See https://github.com/FriendsOfSymfony/FOSCommentBundle/blob/master/Resources/doc/9a-markup_htmlpurifier.md
    markup.exercise_html_purifier:
        class: FOS\CommentBundle\Markup\HtmlPurifier
        arguments: [ @exercise_html_purifier.default ]
        
    # Your own parser    
    markup.my_parser:
        #This is your own parser that implements ParserInterface
        class: MyBundle\Parser\MyParser
```

Then, you have to define PipelineParser as a service and configure it with parsers.

```yaml
    markuper.pipeline_parser:
        class: FOS\CommentBundle\Markup\PipelineParser
        calls:
            - [addToPipeline, ["@markup.exercise_html_purifier"]]
            - [addToPipeline, ["@markup.my_parser"]]
```

The order of parsers is important. First parser that has been added to pipeline is executed first.


The last step is to use pipeline parser as described in [documentation](https://github.com/FriendsOfSymfony/FOSCommentBundle/blob/master/Resources/doc/9-using_a_markup_parser.md)
```yaml
# app/config/config.yml

fos_comment:
    service:
        markup: markuper.pipeline_parser # The pipeline parser service
```
