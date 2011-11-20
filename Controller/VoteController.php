<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Controller;

use FOS\CommentBundle\Model\CommentInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * The VoteController
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class VoteController extends ContainerAware
{
    /**
     * Adds a vote to a comment - Intended to be accessed from ajax.
     *
     * Note that value should be set in the route as a default value without
     * a parameter, otherwise users would be able to change the value of
     * their votes by changing the URL. eg:
     *
     *      # routing.yml
     *
     *      route_name:
     *          pattern: /vote/{commentId}/up
     *          defaults:
     *              _controller: FOSCommentBundle:Vote:add
     *              value: 1
     *
     * @throws NotFoundHttpException when comment is not found
     * @param mixed $commentId The comment id
     * @param integer $value The value of the comment
     * @return Response JSON encoded replacement score for the comment
     */
    public function addAction($commentId, $value)
    {
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);
        if (!$comment) {
            throw new NotFoundHttpException('Comment not found');
        }

        $vote = $this->createVote($value);
        if ($this->container->get('fos_comment.creator.vote')->create($vote, $comment)) {
            return new Response(json_encode(array('score' => $comment->getScore())));
        }

        return new Response('', 400);
    }

    /**
     * Lists all votes for a comment.
     *
     * @throws NotFoundHttpException when the comment is not found
     * @param integer $commentId The comment id
     * @return Response
     */
    public function listAction($commentId)
    {
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);
        if (!$comment) {
            throw new NotFoundHttpException('Comment not found');
        }

        $votes = $this->container->get('fos_comment.manager.vote')->findVotesByComment($comment);

        return $this->container->get('templating')->renderResponse(
            'FOSCommentBundle:Vote:list.html.'.$this->container->getParameter('fos_comment.template.engine'),
            array(
                'comment' => $comment,
                'votes' => $votes,
            )
        );
    }

    /**
     * Creates a vote for a given comment with a supplied value.
     *
     * @param integer $value The value of the vote
     * @return VoteInterface
     */
    public function createVote($value)
    {
        $vote = $this->container->get('fos_comment.manager.vote')->createVote();
        $vote->setValue($value);

        return $vote;
    }
}
