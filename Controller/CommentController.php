<?php

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */

namespace FOS\CommentBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\CommentInterface;

use FOS\CommentBundle\Form\CommentForm;
use Symfony\Component\Form\HiddenField;

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
        $comment = $this->container->get('fos_comment.manager.comment')->createComment();
        $comment->setThread($thread);
        $form = $this->createForm();
        $form->setData($comment);

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Comment:new.html.twig', array(
            'thread' => $thread,
            'form'   => $form
        ));
    }

    /**
     * Submit a comment form
     */
    public function createAction()
    {
        $comment = $this->container->get('fos_comment.manager.comment')->createComment();
        $form = $this->createForm();
        $form->bind($this->container->get('request'), $comment);

        if ($form->isValid()) {
            $this->container->get('fos_comment.manager.comment')->addComment($comment);
            return $this->onCreateSuccess($form);
        }

        return $this->onCreateError($form);
    }

    protected function onCreateSuccess(CommentForm $form)
    {
        return new Response(json_encode(array(
            'success' => true,
            'comment' => $this->container->get('templating')->render('FOSCommentBundle:Comment:show.html.twig', array(
                'comment' => $form->getData()
            ))
        )));
    }

    protected function onCreateError(CommentForm $form)
    {
        return new Response(json_encode(array(
            'success' => false,
            'form'    => $this->container->get('templating')->render('FOSCommentBundle:Comment:new.html.twig', array(
                'form' => $form
            ))
        )));
    }

    protected function createForm()
    {
        $form = CommentForm::create($this->container->get('form.context'), 'fos_comment_create');
        $form->add(new HiddenField('thread', array('value_transformer' => $this->container->get('fos_comment.value_transformer.thread'))));

        return $form;
    }
}
