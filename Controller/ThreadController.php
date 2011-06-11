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

use FOS\CommentBundle\Model\ThreadInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Groups all thread related actions into the controller.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Tim Nagel <tim@nagel.com.au>
 */
class ThreadController extends ContainerAware
{
    /**
     * Show a thread, its comments and the comment form if available
     * There is no routing for this action, call it from a template:
     *
     *    {% render "FOSCommentBundle:Thread:show" with {"identifier": "something_unique"} %}
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

        $comment = $this->container->get('fos_comment.manager.comment')->createComment($thread);
        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);

        $availableSorters = $this->container->get('fos_comment.sorting_factory')->getAvailableSorters();

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Thread:show.html.twig', array(
            'thread' => $thread,
            'sorter' => $sorter,
            'availableSorters' => $availableSorters,
            'displayDepth'  => $displayDepth,
            'form'   => $form->createView(),
            'comment' => $comment
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
}
