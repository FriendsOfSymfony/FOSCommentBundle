<?php

namespace FOS\CommentBundle\Blamer;

use FOS\CommentBundle\Model\VoteInterface;

/**
 * Does nothing.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class NoopVoteBlamer implements VoteBlamerInterface
{
    public function blame(VoteInterface $vote)
    {
        // Do nothing.
    }
}
