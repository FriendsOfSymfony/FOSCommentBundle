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
     * Gets the thread specified by the identifier, and if it does
     * not exist, creates a new one.
     *
     * @param string $identifier
     * @return ThreadInterface
     */
    protected function getThread($identifier)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadByIdentifier($identifier);

        if (!$thread) {
            $thread = $this->container->get('fos_comment.creator.thread')->create($identifier);
        }

        return $thread;
    }

    /**
     * Creates a form to reply to the supplied ThreadInterface
     *
     * @param ThreadInterface $thread
     * @return Form
     */
    protected function getCommentForm(ThreadInterface $thread)
    {
        $comment = $this->container->get('fos_comment.manager.comment')->createComment($thread);

        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);

        return $form;
    }

    /**
     * Show a thread, its comments and the comment form if available
     * There is no routing for this action, call it from a template:
     *
     *    {% render "FOSCommentBundle:Thread:show" with {"identifier": "something_unique"} %}
     *
     * If the thread for the identifier does not exist, it will be created.
     *
     * Available options to pass to this action are:
     *    identifier:   The identifier of the thread.
     *    sorter:       The alias of the sorter to use, or null
     *                  for the default sorter.
     *    displayDepth: The depth of comments to display.
     *
     * @param mixed $identifier
     * @param string $sorter
     * @param integer $displayDepth
     * @return Response
     */
    public function showAction($identifier, $sorter = null, $displayDepth = null)
    {
        $thread = $this->getThread($identifier);
        $newCommentForm = $this->getCommentForm($thread);
        $replyForm = $this->getCommentForm($thread);

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Thread:show.html.twig', array(
            'thread'           => $thread,
            'sorter'           => $sorter,
            'displayDepth'     => $displayDepth,
            'newCommentForm'   => $newCommentForm->createView(),
            'replyForm'        => $replyForm->createView(),
        ));
    }

    /**
     * Show a thread, its comments and the comment form in a flat manner if available
     * There is no routing for this action, call it from a template:
     *
     *    {% render "FOSCommentBundle:Thread:show" with {"identifier": "something_unique"} %}
     *
     * If the thread for the identifier does not exist, it will be created.
     *
     * Available options to pass to this action are:
     *    identifier:   The identifier of the thread.
     *    sorter:       The alias of the sorter to use, or null
     *                  for the default sorter.
     *
     * @param mixed $identifier
     * @param string $sorter
     * @return Response
     */
    public function showFlatAction($identifier, $sorter = null)
    {
        $thread = $this->getThread($identifier);
        $newCommentForm = $this->getCommentForm($thread);
        $replyForm = $this->getCommentForm($thread);

        return $this->container->get('templating')->renderResponse('FOSCommentBundle:Thread:showFlat.html.twig', array(
            'thread'           => $thread,
            'sorter'           => $sorter,
            'newCommentForm'   => $newCommentForm->createView(),
            'replyForm'        => $replyForm->createView(),
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
