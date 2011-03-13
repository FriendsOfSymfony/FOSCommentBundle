<?php

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */

namespace FOS\CommentBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\CommentInterface;

class CommentController extends ContainerAware
{
    /**
     * Show a thread comments
     */
    public function treeAction(ThreadInterface $thread)
    {
        $nodes = $this->container->get('fos_comment.manager.comment')->findCommentsByThread($thread);

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Comment:tree.html.twig', array(
            'nodes' => $nodes
        ));
    }

    /**
     * Show a comment form
     */
    public function newAction(ThreadInterface $thread)
    {
        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Comment:new.html.twig', array(
            'thread' => $thread
        ));
    }
}
