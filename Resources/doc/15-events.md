Step 15 : Create a listener for comment events
==============================================

FOSCommentBundle fires events inside Symfony. It's very handy to add custom
tasks at a precise time, without modifying the controller.

### Events

All the events and their description are listed in the [Events](https://github.com/FriendsOfSymfony/FOSCommentBundle/blob/master/Events.php)
 file.

 - COMMENT_PRE_PERSIST
 - COMMENT_POST_PERSIST
 - COMMENT_CREATE
 - THREAD_PRE_PERSIST
 - THREAD_POST_PERSIST
 - THREAD_CREATE
 - VOTE_PRE_PERSIST
 - VOTE_POST_PERSIST
 - VOTE_CREATE

### Handle an event

You have to create a listener to handle an event.

For example :

``` php
// src/Application/CommentBundle/EventListener/MailNotificationListener.php

<?php

namespace Application\CommentBundle\EventListener;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Listener responsible to send email notifications when a comment is persisted
 */
class MailNotificationListener implements EventSubscriberInterface
{
    /**
    * @var Swift_Mailer
    */
    private $mailer;

    /**
     * Constructor.
     *
     * @param Swift_Mailer     $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::COMMENT_POST_PERSIST => 'onCommentPostPersistTest',
        );
    }

    public function onCommentPostPersist(CommentEvent $event)
    {
        $comment = $event->getComment();

        $message = \Swift_Message::newInstance()
            ->setSubject('New comment of ' . $comment->getAuthor())
            ->setFrom('root@example.com')
            ->setTo('john@doe.com')
            ->setBody(
                $comment->getAuthor() . ' has written ' . $comment->getBody()
            );

          $this->mailer->send($message);
    }
}
```

Now you need to register you Listener in `Service.xml` file

``` xml
<service id="application_comment.listener.comment" class="Application\CommentBundle\EventListener\MailNotificationListener">
  <argument type="service" id="mailer" />
  <tag name="kernel.event_listener" event="fos_comment.comment.post_persist" method="onCommentPostPersist" />
</service>
```

This is where you indicate which Event you're listening to, and which method is
needed to be called when the Event is fired.

## That is it!
[Return to the index.](index.md)
