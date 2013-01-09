<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Functional\Bundle\CommentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Test controller used in the functional tests for CommentBundle
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentController extends Controller
{
    public function asyncAction($id)
    {
        return $this->render('CommentBundle:Comment:async.html.twig', array(
            'id' => $id,
        ));
    }

    public function inlineAction(Request $request, $id)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (null === $thread) {
            $thread = $this->container->get('fos_comment.manager.thread')->createThread();
            $thread->setId($id);
            $thread->setPermalink($request->getUri());

            // Add the thread
            $this->container->get('fos_comment.manager.thread')->saveThread($thread);
        }

        $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        return $this->render('CommentBundle:Comment:inline.html.twig', array(
            'comments' => $comments,
            'thread' => $thread,
        ));
    }
}
