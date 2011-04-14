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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Form\CommentForm;
use Symfony\Component\Form\HiddenField;

class ThreadController extends ContainerAware
{
    /**
     * Show a thread, its comments and the comment form if available
     * There is no routing for this action, call it from a template:
     *
     *    {% render "FOSComment:Thread:show" with {"identifier": "something_unique"} %}
     *
     * If the thread for the identifier does not exist, it will be created.
     *
     * @param mixed $identifier
     * @param string $sorter
     * @param integer $displayDepth
     * @return Response
     */
    public function showAction($identifier, $sorter = null, $displayDepth = null)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadByIdentifier($identifier);
        if (!$thread) {
            $thread = $this->container->get('fos_comment.creator.thread')->create($identifier);
        }

        $comment = $this->createComment($thread);
        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);

        $availableSorters = $this->container->get('fos_comment.sorting_factory')->getAvailableSorters();

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Thread:show.html.twig', array(
            'thread' => $thread,
            'sorter' => $sorter,
            'availableSorters' => $availableSorters,
            'displayDepth'  => $displayDepth,
            'form'   => $form
        ));
    }

    /**
     * Show an xml feed for a thread.
     *
     * @param mixed $identifier
     * @return Response
     */
    public function showFeedAction($identifier)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadByIdentifier($identifier);
        if (!$thread) {
            throw new NotFoundHttpException(sprintf('No comment thread with identifier "%s"', $identifier));
        }

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Thread:showFeed.xml.twig', array(
            'thread' => $thread
        ));
    }

    /**
     * Creates a comment that belongs to the provided Thread.
     *
     * @param ThreadInterface $thread
     * @return CommentInterface
     */
    protected function createComment(ThreadInterface $thread)
    {
        $comment = $this->container->get('fos_comment.manager.comment')->createComment();
        $comment->setThread($thread);

        return $comment;
    }
}
