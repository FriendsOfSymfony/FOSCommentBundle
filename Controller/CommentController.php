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

use FOS\CommentBundle\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Restful controller for the Threads.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class CommentController extends Controller
{
    public function showAction(Request $request)
    {
        $id          = $request->get('id');
        $redirectUri = $request->get('redirectUri', false);

        /** @var Thread $thread */
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);

        if (null === $thread) {
            $thread = $this->container->get('fos_comment.manager.thread')->createThread();
            $thread->setId($id);
            $thread->setPermalink($request->getUri());

            // Add the thread
            $this->container->get('fos_comment.manager.thread')->saveThread($thread);
        }

        $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        return $this->render('FOSCommentBundle:Sync:comments.html.twig', array(
            'comments'     => $comments,
            'thread'       => $thread,
            'redirect_uri' => $redirectUri
        ));
    }

    public function showCountAction(Request $request)
    {
        $id = $request->get('id');

        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);

        if (!$thread) {
            return new Response();
        }

        return $this->render('FOSCommentBundle:Sync:comment_count.html.twig', array(
            'comment_count' => $thread->getNumComments()
        ));
    }
}