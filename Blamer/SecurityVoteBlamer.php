<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Blamer;

use FOS\CommentBundle\Model\SignedVoteInterface;
use FOS\CommentBundle\Model\VoteInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Assigns a FOS\UserBundle user from the logged in user to a vote.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class SecurityVoteBlamer implements VoteBlamerInterface
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * Constructor.
     *
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * Assigns the Security token's user to the vote.
     *
     * @throws InvalidArgumentException when the vote does not implement SignedVoteInterface
     * @throws RuntimeException When the firewall is not properly configured
     * @param VoteInterface $vote
     * @return void
     */
    public function blame(VoteInterface $vote)
    {
        if (!$vote instanceof SignedVoteInterface) {
            throw new InvalidArgumentException('The vote must implement SignedVoteInterface');
        }

        if (null === $this->securityContext->getToken()) {
            throw new RuntimeException('You must configure a firewall for this route');
        }

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $vote->setVoter($this->securityContext->getToken()->getUser());
        }
    }
}
