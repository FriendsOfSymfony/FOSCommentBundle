<?php

namespace FOS\CommentBundle\Blamer;

use FOS\CommentBundle\Model\SignedCommentInterface;

use Symfony\Component\Security\Core\SecurityContext;

/**
 * Blames a comment using Symfony2 security component
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class SecurityCommentBlamer implements CommentBlamerInterface
{
    protected $securityContext;

    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function blame(SignedCommentInterface $comment)
    {
        if ($this->securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $comment->setAuthor($this->securityContext->getToken()->getUser());
        }
    }
}
