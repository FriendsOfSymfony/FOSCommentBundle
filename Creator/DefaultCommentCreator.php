<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Blamer\CommentBlamerInterface;
use FOS\CommentBundle\Model\CommentManagerInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\SpamDetection\SpamDetectionInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Responsible for primary creation and persistance of a Comment object.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class DefaultCommentCreator implements CommentCreatorInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var CommentManagerInterface
     */
    protected $commentManager;

    /**
     * @var CommentBlamerInterface
     */
    protected $commentBlamer;

    /**
     * @var SpamDetectionInterface
     */
    protected $spamDetection;

    /**
     * Constructor.
     *
     * @param Request $request
     * @param CommentManagerInterface $commentManager
     * @param CommentBlamerInterface $commentBlamer
     * @param SpamDetectionInterface $spamDetection
     */
    public function __construct(Request $request, CommentManagerInterface $commentManager, CommentBlamerInterface $commentBlamer, SpamDetectionInterface $spamDetection)
    {
        $this->request        = $request;
        $this->commentManager = $commentManager;
        $this->commentBlamer  = $commentBlamer;
        $this->spamDetection  = $spamDetection;
    }

    /**
     * {@inheritDoc}
     */
    public function create(CommentInterface $comment)
    {
        $this->commentBlamer->blame($comment);

        if ($this->spamDetection->isSpam($comment)) {
            return false;
        }

        $parent = $this->commentManager->findCommentById($this->request->request->get('reply_to'));

        $this->commentManager->addComment($comment, $parent);

        return true;
    }
}
