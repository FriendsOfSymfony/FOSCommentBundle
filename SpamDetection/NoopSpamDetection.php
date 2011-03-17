<?php

namespace FOS\CommentBundle;

use FOS\CommentBundle\Model\CommentInterface;

class NoopSpamDetection implements SpamDetectionInterface
{
    public function isSpam(CommentInterface $comment)
    {
        return false;
    }
}
