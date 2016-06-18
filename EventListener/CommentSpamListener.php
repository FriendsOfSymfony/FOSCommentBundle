<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\EventListener;

use FOS\CommentBundle\Events;
use FOS\CommentBundle\Event\CommentPersistEvent;
use FOS\CommentBundle\SpamDetection\SpamDetectionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Psr\Log\LoggerInterface;

/**
 * A listener that checks if a comment is spam based on a service
 * that implements SpamDetectionInterface.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentSpamListener implements EventSubscriberInterface
{
    /**
     * @var SpamDetectionInterface
     */
    protected $spamDetector;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor.
     *
     * @param SpamDetectionInterface $detector
     * @param LoggerInterface        $logger
     */
    public function __construct(SpamDetectionInterface $detector, LoggerInterface $logger = null)
    {
        $this->spamDetector = $detector;
        $this->logger = $logger;
    }

    public function spamCheck(CommentPersistEvent $event)
    {
        $comment = $event->getComment();

        if ($this->spamDetector->isSpam($comment)) {
            if (null !== $this->logger) {
                $this->logger->info('Comment is marked as spam from detector, aborting persistence.');
            }

            $event->abortPersistence();
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(Events::COMMENT_PRE_PERSIST => 'spamCheck');
    }
}
