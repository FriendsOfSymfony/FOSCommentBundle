Step 13: Hooking into the JS code
=================================

The FOSCommentBundle Js code fires events on the thread container object.
These events are triggered, for example, when users cancel a comment or reply to a comment.


### fos_comment_before_load_thread( identifier )

Triggered before getting the comments of a thread and placing them in the thread holder.

- identifier: unique identifier url for the thread comments

### fos_comment_load_thread( identifier )

Triggered after placing retrieved comments in the thread holder.

- identifier: unique identifier url for the thread comments

### fos_comment_new_comment( data )

Triggered if the request about a new comment submission succeeds.

- data: data sent to the server with the request.


### fos_comment_submitted_form( statusCode )

Triggered when the request about a new comment submission is completed.

 - statusCode: status of the server response


### fos_comment_submitting_form(  )

Triggered before posting the new comment form.

Preventing the default action will cancel the submission of the comment.

### fos_comment_show_form( data )

Triggered when the reply form is inserted into the DOM tree.

 - data: the reply form content


### fos_comment_cancel_form(  )

Triggered when the comment reply is closed.

Preventing the default action will cancel closing the form.

### fos_comment_edit_comment( data )

Triggered if the request about editing a comment succeeds.

 - data: data sent to the server with the request.

### fos_comment_vote_comment( data )

Triggered when the the request about voting a comment succeeds.

 - data: data sent to the server with the request.


### fos_comment_add_comment( commentHtml )

Triggered when the comment is inserted into the DOM tree.

 - commentHtml: jQuery object to insert into the DOM tree.


### fos_comment_removing_comment(  )

Triggered before a comment delete action.

Preventing the default action will cancel the removal.


### fos_comment_show_edit_form( data )

Triggered when the edit form is inserted into the DOM tree.

 - data: the edit form content


Example:
=============================

```js
$(document)
    .on('fos_comment_show_form', '.fos_comment_comment_reply_show_form', function (event, data) {
        // do stuffs
    });
```

## That is it!
[Return to the index.](index.md)
