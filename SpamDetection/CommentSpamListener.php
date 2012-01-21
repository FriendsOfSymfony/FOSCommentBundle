<?php

namespace FOS\CommentBundle\SpamDetection;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentPersistEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommentSpamListener implements EventSubscriberInterface
{
    protected $spamDetector;

    public function __construct(SpamDetectionInterface $detector)
    {
        $this->spamDetector = $detector;
    }

    public function spamCheck(CommentPersistEvent $event)
    {
        $comment = $event->getComment();

        if ($this->spamDetector->isSpam($comment)) {
            $event->abortPersist();
        }
    }

    static public function getSubscribedEvents()
    {
        return array(Events::COMMENT_PRE_PERSIST, 'spamCheck');
    }
}