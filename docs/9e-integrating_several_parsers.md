Step 9e: Integrating several parsers together
=============================================

Purpose
--------
Imagine that you need to integrate ExerciseHTMLPurifierBundle parser, bbcode parser, any other parser... it's better to have several parsers where each is responsible for its action. That's why it's better to make chain of parsers, where order is important and parsers works one after another.

Usage
-----

First, configure parser classes as services.

```yaml
    # See 9a-markup_htmlpurifier.md
    markup.exercise_html_purifier:
        class: FOS\CommentBundle\Markup\HtmlPurifier
        arguments: [ @exercise_html_purifier.default ]
        # If you don't plan to use this parser somewhere in the code, it's better to make it private.
        # this should increase service container performance
        public: false        
        
    # Your own parser    
    markup.my_parser:
        #This is your own parser that implements ParserInterface
        class: MyBundle\Parser\MyParser
        # If you don't plan to use this parser somewhere in the code, it's better to make it private.
        # this should increase service container performance
        public: false
```

Then, you have to define PipelineParser as a service and configure it with parsers.

```yaml
    markuper.pipeline_parser:
        class: FOS\CommentBundle\Markup\PipelineParser
        calls:
            - [addToPipeline, ["@markup.exercise_html_purifier"]]
            - [addToPipeline, ["@markup.my_parser"]]
```

Parsers are executed in the order in which they are added to the pipeline.


The last step is to use pipeline parser as described in [documentation](9-using_a_markup_parser.md)
```yaml
# app/config/config.yml

fos_comment:
    service:
        markup: markuper.pipeline_parser # The pipeline parser service
```
