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
     * Submit a comment form
     */
    public function createAction()
    {
        $comment = $this->container->get('fos_comment.manager.comment')->createComment();
        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->bind($this->container->get('request'), $comment);

        if ($form->isValid()) {
            $this->container->get('fos_comment.blamer.comment')->blame($comment);
            $parent = $this->container->get('fos_comment.manager.comment')->findCommentById(
                $this->container->get('request')->request->get('reply_to')
            );
            $this->container->get('fos_comment.manager.comment')->addComment($comment, $parent);

            return $this->onCreateSuccess($form);
        }

        return $this->onCreateError($form);
    }

    protected function onCreateSuccess(CommentForm $form)
    {
        return $this->container->get('http_kernel')->forward('FOSCommentBundle:Thread:show', array(
            'identifier' => $form->getData()->getThread()->getIdentifier()
        ));
    }

    protected function onCreateError(CommentForm $form)
    {
        return new Response("", 400);
    }
}
