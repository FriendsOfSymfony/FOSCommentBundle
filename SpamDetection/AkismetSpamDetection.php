<?php

namespace FOS\CommentBundle\SpamDetection;

use Zend\Service\Akismet\Akismet as ZendAkismet;
use Zend\Service\Akismet\Exception as AkismetException;
use Symfony\Component\HttpFoundation\Request;
use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\SignedCommentInterface;

class AkismetSpamDetection implements SpamDetectionInterface
{
    protected $request;
    protected $akismet;

    public function __construct(Request $request, ZendAkismet $akismet)
    {
        $this->request = $request;
        $this->akismet = $akismet;
    }

    public function isSpam(CommentInterface $comment)
    {
        $data = array_merge($this->getRequestData(), $this->getCommentData($comment));

        try {
            return $this->akismet->isSpam($data);
        } catch (AkismetException $e) {
            return true;
        }
    }

    protected function getCommentData(CommentInterface $comment)
    {
        $data = array(
            'comment_type'    => 'comment',
            'comment_content' => $comment->getBody()
        );

        if ($comment instanceof SignedCommentInterface) {
            $data['comment_author'] = $comment->getAuthorName();
        }

        return $data;
    }

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
