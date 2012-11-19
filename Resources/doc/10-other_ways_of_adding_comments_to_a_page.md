Step 10: Other ways of including comments in a page
======================================

The default implementation of FOSCommentBundle uses asynchronous javascript
and jQuery (optionally with easyXDM for cross domain requests) to load a comment
thread into a page.

It is possible to include the thread without using javascript to load it, but
needs additional work inside the controller's action.

At a minimum, you will need to include the following in your action's PHP code:

``` php
public function somethingAction(Request $request)
{
    $id = 'thread_id';
    $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
    if (null === $thread) {
        $thread = $this->container->get('fos_comment.manager.thread')->createThread();
        $thread->setId($id);
        $thread->setPermalink($request->getUri());

        // Add the thread
        $this->container->get('fos_comment.manager.thread')->saveThread($thread);
    }

    $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

    return $this->render('AcmeDemoBundle:Controller:something.html.twig', array(
        'comments' => $comments,
        'thread' => $thread,
    ));
}
```

Once you've included this code in your action, some code must be included in your
template:

``` jinga
{% block body %}
{# ... #}
<div id="fos_comment_thread" data-thread="{{ thread.id }}">

{% include 'FOSCommentBundle:Thread:comments.html.twig' with {
    'comments': comments,
    'thread': thread
} %}

</div>
{# ... #}
{% endblock body %}

{% block javascript %}
{# jQuery must be available in the page by this time, and make sure javascript block is after
  <div id="fos_comment_thread"> in the DOM Tree, for example right before </body> tag
#}
{% javascripts '@FOSCommentBundle/Resources/assets/js/comments.js' %}
<script type="text/javascript" src="{{ asset_url }}"></script>
{% endjavascripts %}
{% endblock javascript %}

```

## That is it!
[Return to the index.](index.md)
