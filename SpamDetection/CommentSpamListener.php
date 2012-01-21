<?php

namespace FOS\CommentBundle\SpamDetection;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CommentSpamListener implements EventSubscriberInterface
{
    protected $spamDetector;

    public function __construct(SpamDetectionInterface $detector)
    {
        $this->spamDetector = $detector;
    }

    public function spamCheck(CommentEvent $event)
    {
        $comment = $event->getComment();

        if ($this->spamDetector->isSpam($comment)) {
            throw new 
        }
    }


    static function getSubscribedEvents()
    {
        return array(Events::COMMENT_PRE_PERSIST, 'spamCheck');
    }
}