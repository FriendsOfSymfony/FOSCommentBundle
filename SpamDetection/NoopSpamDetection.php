<?php

namespace FOS\CommentBundle\SpamDetection;

use FOS\CommentBundle\Model\CommentInterface;

class NoopSpamDetection implements SpamDetectionInterface
{
    public function isSpam(CommentInterface $comment)
    {
        return false;
    }
}
