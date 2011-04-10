<?php

namespace FOS\CommentBundle\Blamer;

use FOS\CommentBundle\Model\VoteInterface;

/**
 * Applies an owner to a vote
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface VoteBlamerInterface
{
    function blame(VoteInterface $vote);
}
