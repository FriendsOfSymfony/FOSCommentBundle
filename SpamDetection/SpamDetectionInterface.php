<?php

namespace FOS\CommentBundle\SpamDetection;

use FOS\CommentBundle\Model\CommentInterface;

interface SpamDetectionInterface
{
    function isSpam(CommentInterface $comment);
}
