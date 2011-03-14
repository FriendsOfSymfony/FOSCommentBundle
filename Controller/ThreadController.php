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

        $comment = $this->container->get('fos_comment.manager.comment')->createComment();
        $comment->setThread($thread);
        $form = CommentForm::create($this->container->get('form.context'), 'fos_comment_create');
        $form->add(new HiddenField('thread', array('value_transformer' => $this->container->get('fos_comment.value_transformer.thread'))));
        $form->setData($comment);

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Thread:show.html.twig', array(
            'thread' => $thread,
            'form'   => $form
        ));
    }
}
