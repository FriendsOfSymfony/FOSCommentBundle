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

use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $nodes = $this->get('fos_comment.manager.comment')->findCommentTreeByThread($thread, $sorter, $displayDepth);

        return $this->get('templating')->renderResponse('FOSCommentBundle:Comment:tree.html.twig', array(
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
        if (!$nodes = $this->get('fos_comment.manager.comment')->findCommentTreeByCommentId($commentId, $sorter))
            throw new NotFoundHttpException('No comment branch found');

        return $this->get('templating')->renderResponse('FOSCommentBundle:Comment:subtree.html.twig', array(
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
        $nodes = $this->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        return $this->get('templating')->renderResponse('FOSCommentBundle:Comment:listFeed.xml.twig', array(
            'nodes'     => $nodes,
            'permalink' => $thread->getPermalink()
        ));
    }

    /**
     * Submit a comment form.
     *
     * @param mixed Thread Identifier
     * @return Response
     */
    public function createAction($threadIdentifier, $parentId = null)
    {
        $thread = $this->get('fos_comment.manager.thread')->findThreadByIdentifier($threadIdentifier);
        if (!$thread) {
            throw new NotFoundHttpException(sprintf('Thread with identifier of "%s" does not exist', $threadIdentifier));
        }

        if (null !== $parentId) {
            $parent = $this->get('fos_comment.manager.comment')->findCommentById($parentId);
            
            if (!$parent) {
                throw new NotFoundHttpException(sprintf('Parent comment with identifier "%s" does not exist', $parentId));
            }
        } else {
            $parent = null;
        }

        $comment = $this->get('fos_comment.manager.comment')->createComment($thread, $parent);

        $form = $this->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);

        $request = $this->get('request');
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid() && $this->get('fos_comment.creator.comment')->create($comment)) {
                return $this->onCreateSuccess($form);
            }
        }

        return $this->onCreateError($form);
    }

    /**
     * Forwards the action to the thread view on a successful form submission.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateSuccess(Form $form)
    {
        return $this->get('http_kernel')->forward('FOSCommentBundle:Thread:show', array(
            'identifier' => $form->getData()->getThread()->getIdentifier()
        ));
    }

    /**
     * Returns a 400 response when the form submission fails.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateError(Form $form)
    {
        return new Response('An Error occurred with form submission', 400);
    }
}
