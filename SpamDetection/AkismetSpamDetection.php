<?php

/**
 * This file is part of the FOS\CommentBundle.
 *
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\SpamDetection;

use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Symfony\Component\HttpFoundation\Request;
use Zend\Service\Akismet\Akismet as ZendAkismet;
use Zend\Service\Akismet\Exception as AkismetException;

/**
 * Detects spam by querying the Akismet service.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class AkismetSpamDetection implements SpamDetectionInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Akismet
     */
    protected $akismet;

    /**
     * Constructor.
     *
     * @param Request $request
     * @param Akismet $akismet
     */
    public function __construct(Request $request, ZendAkismet $akismet)
    {
        $this->request = $request;
        $this->akismet = $akismet;
    }

    /**
     * Returns true if Akismet believes the comment to be spam.
     *
     * @param CommentInterface $comment
     * @return bool
     */
    public function isSpam(CommentInterface $comment)
    {
        $data = array_merge($this->getRequestData(), $this->getCommentData($comment));

        try {
            return $this->akismet->isSpam($data);
        } catch (AkismetException $e) {
            return true;
        }
    }

    /**
     * Compiles comment data into a format Akismet accepts.
     *
     * @param CommentInterface $comment
     * @return array
     */
    protected function getCommentData(CommentInterface $comment)
    {
        $data = array(
            'comment_type'    => 'comment',
            'comment_content' => $comment->getBody()
        );

        $data['comment_author'] = $comment->getAuthorName();

        return $data;
    }

    /**
     * Compiles a list of information to assist Akismet in detecting spam.
     *
     * @return array
     */
    protected function getRequestData()
    {
        return array(
            'permalink'  => $this->request->getUri(),
            'user_ip'    => $this->request->getClientIp(),
            'user_agent' => $this->request->server->get('HTTP_USER_AGENT'),
            'referrer'   => $this->request->server->get('HTTP_REFERER'),
        );
    }
}
