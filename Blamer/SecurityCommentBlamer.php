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

use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use InvalidArgumentException;
use RuntimeException;

/**
 * Blames a comment using Symfony2 security component
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class SecurityCommentBlamer implements CommentBlamerInterface
{
    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * Constructor.
     *
     * @param SecurityContext $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * Assigns the currently logged in user to a Comment.
     *
     * @throws InvalidArgumentException when the Comment is not a SignedCommentInterface
     * @param CommentInterface $comment
     * @return void
     */
    public function blame(CommentInterface $comment)
    {
        if (!$comment instanceof SignedCommentInterface) {
            throw new InvalidArgumentException('The comment must implement SignedCommentInterface');
        }

        if (null === $this->securityContext->getToken()) {
            throw new RuntimeException('You must configure a firewall for this route');
        }

        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $comment->setAuthor($this->securityContext->getToken()->getUser());
        }
    }
}
