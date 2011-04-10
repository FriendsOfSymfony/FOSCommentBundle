<?php

namespace FOS\CommentBundle\Blamer;

use FOS\CommentBundle\Model\SignedVoteInterface;
use FOS\CommentBundle\Model\VoteInterface;
use Symfony\Component\Security\Core\SecurityContext;
use InvalidArgumentException;

/**
 * Assigns a FOS\UserBundle user from the logged in user to a vote.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SecurityVoteBlamer implements VoteBlamerInterface
{
    protected $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function blame(VoteInterface $vote)
    {
        if (!$vote instanceof SignedVoteInterface) {
            throw new InvalidArgumentException('The vote must implement SignedVoteInterface');
        }

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $vote->setVoter($this->securityContext->getToken()->getUser());
        }
    }
}
