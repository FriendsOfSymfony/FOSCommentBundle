Step 14 : Create a listener for comment events
==============================================

FOSCommentBundle fires events inside Symfony. It's very handy to add custom tasks at a precise time, without modifying the controller.

### Events

All the events and their description are listed in the [Events](https://github.com/FriendsOfSymfony/FOSCommentBundle/blob/master/Events.php) file.

 - fos_comment.comment.pre_persist
 - fos_comment.comment.post_persist
 - fos_comment.comment.create
 - fos_comment.thread.pre_persist
 - fos_comment.thread.post_persist
 - fos_comment.thread.create
 - fos_comment.vote.pre_persist
 - fos_comment.vote.post_persist
 - fos_comment.vote.create
 
### Handle an event

You have to create a listener to handle an event.

Here is a little exemple.

    namespace MyProject\NewsBundle\Listener;

    use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
    use FOS\CommentBundle\Event\CommentEvent;
    use FOS\CommentBundle\Model\CommentManagerInterface;

    class CommentListener
    {
        /**
        * If not needed, you can delete this (don't forget to delete it in the service description in service.xml)
        * @var CommentManagerInterface
        */
        private $commentManager;
	
        /**
        * If not needed, you can delete this (don't forget to delete it in the service description in service.xml)
        * @var Doctrine 
        */
        private $doctrine;

        /**
        * Constructor.
        *
        * @param CommentManagerInterface $commentManager
        */
        public function __construct(CommentManagerInterface $commentManager,  Doctrine $doctrine)
        {
            $this->commentManager = $commentManager;
            $this->doctrine = $doctrine;
        }
	
        /**
        * Method called on fos_comment.comment.post_persist
        *
        * @param \FOS\CommentBundle\Event\CommentEvent $event
        */
        public function onCommentPersist(CommentEvent $event)
        {
		
            /* @var $comment \FOS\CommentBundle\Model\CommentInterface */
            $comment = $event->getComment();
		
            /* do whatever you want in here */
		
            /* you can access doctrine easily : */
            $post = $this->doctrine->getEntityManager()->getRepository('MyProjectNewsBundle:Post')->find($postId);
		
		
        }
    }

Now you need to register you Listener in `Service.xml` file

    <service id="myproject.news.listener.comment" class="MyProject\NewsBundle\Listener\CommentListener">
        <argument type="service" id="fos_comment.manager.comment" />
        <argument type="service" id="doctrine" />
        <tag name="kernel.event_listener" event="fos_comment.comment.post_persist" method="onCommentPersist" />
    </service>

This is where you indicate which Event you're listening to, and which method is needed to be called when the Event is fired.

## That is it!
[Return to the index.](index.md)
