<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Blamer\CommentBlamerInterface;
use FOS\CommentBundle\Model\CommentManagerInterface;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\SpamDetection\SpamDetectionInterface;

/**
 * Responsible for primary creation and persistance of a Comment object.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class DefaultCommentCreator implements CommentCreatorInterface
{
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
     * @param CommentManagerInterface $commentManager
     * @param CommentBlamerInterface $commentBlamer
     * @param SpamDetectionInterface $spamDetection
     */
    public function __construct(CommentManagerInterface $commentManager, CommentBlamerInterface $commentBlamer, SpamDetectionInterface $spamDetection)
    {
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

        $this->commentManager->addComment($comment);

        return true;
    }
}
