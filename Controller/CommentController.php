<?php

/**
 * This file is part of the FOS\CommentBundle.
 *
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\CommentInterface;

use FOS\CommentBundle\Form\CommentForm;

/**
 * Groups all comment related actions into the controller.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Tim Nagel <tim@nagel.com.au>
 */
class CommentController extends ContainerAware
{
    /**
     * Shows a thread comments tree.
     *
     * @param ThreadInterface $thread
     * @param string $sorter
     * @param integer $displayDepth
     * @return Response
     */
    public function treeAction(ThreadInterface $thread, $sorter = null, $displayDepth = null)
    {
        $nodes = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread, $sorter, $displayDepth);

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Comment:tree.html.twig', array(
            'nodes' => $nodes,
            'displayDepth' => $displayDepth,
            'sorter' => $sorter,
        ));
    }

    /**
     * Loads a tree branch of comments.
     *
     * @param integer $commentId
     * @param string $sorter
     * @return Response
     */
    public function subtreeAction($commentId, $sorter = null)
    {
        if (!$nodes = $this->container->get('fos_comment.manager.comment')->findCommentTreeByCommentId($commentId, $sorter))
            throw new NotFoundHttpException('No comment branch found');

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Comment:subtree.html.twig', array(
            'nodes' => $nodes,
            'depth' => $nodes[0]['comment']->getDepth(),
            'sorter' => $sorter,
        ));
    }

    /**
     * Shows a thread comments list.
     *
     * @param ThreadInterface $thread
     * @return Response
     */
    public function listFeedAction(ThreadInterface $thread)
    {
        $nodes = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Comment:listFeed.xml.twig', array(
            'nodes'     => $nodes,
            'permalink' => $thread->getPermalink()
        ));
    }

    /**
     * Submit a comment form.
     *
     * @return Response
     */
    public function createAction()
    {
        $comment = $this->container->get('fos_comment.manager.comment')->createComment();
        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->bind($this->container->get('request'), $comment);

        if ($form->isValid() && $this->container->get('fos_comment.creator.comment')->create($comment)) {
            return $this->onCreateSuccess($form);
        }

        return $this->onCreateError($form);
    }

    /**
     * Forwards the action to the thread view on a successful form submission.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateSuccess(CommentForm $form)
    {
        return $this->container->get('http_kernel')->forward('FOSCommentBundle:Thread:show', array(
            'identifier' => $form->getData()->getThread()->getIdentifier()
        ));
    }

    /**
     * Returns a 400 response when the form submission fails.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateError(CommentForm $form)
    {
        return new Response("", 400);
    }
}
