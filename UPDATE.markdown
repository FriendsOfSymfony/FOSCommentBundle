2.0.x:

 * The dependencies to FOS RestBundle have changed and as a result, you need to ensure that you have defined
   a format_listener rule for comment-bundle, for example:

```yaml
fos_rest:
   format_listener:
         rules:
           - { path: '^/comments', priorities: ['json', 'html'], fallback_format: json }
```

 * [BC BREAK] `VoteInterface` signature has been modified. The type hint of
   `VoteInterface::isVoteValid()` has been removed to support symfony 3.0.

2.0.4 to 2.0.5
 * Editing is more robust and works even if you change the structure of the HTML
   code. If you have customised the "comment_content.html.twig" you might need to
   apply the changes made in this version to your customisation.

2.0.3 to 2.0.4
 * Symfony 2.2 compatibility

2.0.2 to 2.0.3

 * A recent change to FOSRestBundle now means that JMSSerializerBundle needs to
   be specified explicitly in your application configuration.
 * The `ThreadController` now decodes the provided permalink before setting it on
   the `Thread` object.
 * The `ThreadController` now validates that a `Thread` object is valid according
   to the Symfony2 Validator metadata.
 * The example CSS now uses more general selectors
 * **The example Javascript is deprecated and will be replaced in 3.0.0**
 * The example javascript now fires [some events] to provide extension points.
 * The example javascript now uses classes instead of traversal making it less
   reliant on the structure of the markup.
 * The example javascript now resets the entire reply form instead of blanking
   only the textarea for the comment body.
 * Edit and Delete are now denied by default. Implement ACL to provide access.

2.0.1 to 2.0.2

 * No changes required.

2.0.0 to 2.0.1

 * Configuration for form names has changed swapping all instances of `.` with `_`.

1.1.x to 2.0.0

 * No changes are required. 2.0.0 primarily introduces Symfony 2.1 support
   while breaking BC with Symfony 2.0.

1.0.0 to 1.1.0

 * `Resources/Thread/comment.html.twig` has changed, adding a rawBody option. This
   change is not relevant unless you are going to use RawComments
 * If you don't use the async template to render the comments, you will need to add
   a new variable defining the base url of the api:

 ``` javascript
 var fos_comment_thread_api_base_url = 'http://example.org/api/threads';
 var fos_comment_thread_id = 'my_thread_id';
 ```
 * A new method `ThreadManagerInterface#findThreadsBy` was added.
 * A new method `ThreadManagerInterface#isNewThread()` was added.
 * `ThreadInterface#setIsCommentable` was renamed to `ThreadInterface#setCommentable`
 * A new method `CommentManagerInterface#isNewComment` was added.
 * The html class `fos_comment_comment_form` was renamed to
   `fos_comment_comment_new_form`. Custom javascript implementations should be
   adjusted for this change.
 * A new method `CommentInterface#getState` was added.
 * A new method `CommentInterface#setState` was added.
 * A new method `CommentInterface#getPreviousState` was added.
 * A new field was added to `Document\Comment` and `Entity\Comment`. ORM users
   should update their schema.


0.9.2 to 1.0.0

 * You need to remove comment.js previously used by this bundle.
   async.html.twig now includes its own javascript file automatically.
 * There is now a dependency on FOSRestBundle. Check the installation documentation
   for details.
 * Routing has changed, you must replace your existing fos_comment route import to

   ``` yaml
   fos_comment_api:
       type: rest
       resource: "@FOSCommentBundle/Resources/config/routing.yml"
       prefix: /comment/api
   ```

 * The way to include comments in a page has changed, importing an asynchronous
   javascript template into your page which will trigger an asynchronous load
   of a comment thread using the REST api.

   ``` jinja
   {% include 'FOSCommentBundle:Thread:async.html.twig' with {'id': 'foo'} %}
   ```

2012-01-21

 * Blamers, Creators and Spam Detection classes have been moved to an Event
   Dispatcher based set up. Documentation on how to use this feature is expected
   to be available with the release of v1.0.0
 * CommentManager, ThreadManager and VoteManager's interfaces have changed
   slightly, renaming add*() methods to save*().

2011-08-10

 * ORM: Column names like ``createdAt`` have been changed to underscore delimited
   format ``created_at``. Schema update and cache clearance is required for
   migration.

2011-08-08

 * Thread property ``identifier`` has been renamed to ``id``
 * ORM: Comment property ancestors has been marked as not null and should default
   to an empty string

2011-07-31

 * The supplied Thread classes are now mapped-superclasses, you must extend and
   implement an appropriate class from Entity/ or Document/ for your application
   and adjust your configuration accordingly.

[some events]: https://github.com/FriendsOfSymfony/FOSCommentBundle/blob/master/Resources/doc/13-hooking-into-the-js-code.md
