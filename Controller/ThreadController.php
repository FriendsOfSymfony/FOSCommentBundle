<?php

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */

namespace FOS\CommentBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\CommentBundle\Form\CommentForm;
use Symfony\Component\Form\HiddenField;

class ThreadController extends ContainerAware
{
    /**
     * Show a thread, its comments and the comment form if available
     */
    public function showAction($identifier)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadByIdentifierOrCreate($identifier);
        if (!$thread) {
            throw new NotFoundHttpException(sprintf('No comment thread with identifier "%s"', $identifier));
        }

        $comment = $this->container->get('fos_comment.manager.comment')->createComment();
        $comment->setThread($thread);

        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Thread:show.html.twig', array(
            'thread' => $thread,
            'form'   => $form
        ));
    }
}
