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

use FOS\CommentBundle\Model\CommentInterface;
use FOS\CommentBundle\Model\ThreadInterface;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Restful controller for the Flags.
 *
 * @author Hubert Brylkowski <hubert@brylkowski.com>
 */
class CommentController extends Controller
{

    /**
     * Presents the form to use to create a new Flag.
     *
     * @return View
     */
    public function newCommentsFlagsAction(Request $request, $id)
    {
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($id);
        if (!$comment) {
            throw new NotFoundHttpException(sprintf('Comment with identifier of "%s" does not exist', $id));
        }

        $flag = $this->container->get('fos_comment.manager.flag')->createFlag($comment);

        $form = $this->container->get('fos_comment.form_factory.flag')->createForm();
        $form->setData($flag);

        $view = View::create()
            ->setData(array(
                          'form' => $form->createView(),
                          'id' => $id,
                      ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Comment', 'flag_new'));

        return $this->getViewHandler()->handle($view);
    }


    public function postCommentsFlagsAction(Request $request, $id)
    {
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($id);
        if (!$comment) {
            throw new NotFoundHttpException(sprintf('Comment with identifier of "%s" does not exist', $id));
        }

        $flagManager = $this->container->get('fos_comment.manager.flag');
        $flag = $flagManager->createFlag($comment);

        $form = $this->container->get('fos_comment.form_factory.flag')->createForm();
        $form->setData($flag);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($flagManager->saveFlag($flag) !== false) {
                return $this->getViewHandler()->handle($this->onCreateFlagSuccess($id));
            }
        }

        return $this->getViewHandler()->handle($this->onCreateFlagError($form, $id));
    }

    public function getCommentsFlagsAction(Request $request, $id)
    {
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($id);
        if (!$comment) {
            throw new NotFoundHttpException(sprintf('Comment with identifier of "%s" does not exist', $id));
        }

        $flagManager = $this->container->get('fos_comment.manager.flag');
        $flags = $flagManager->findFlagsByComment($comment);

        $view = View::create()
            ->setData(array(
                          'flags' => $flags,
                          'id' => $id,
                      ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Comment', 'flags'));

        return $this->getViewHandler()->handle($view);
    }


    /**
     * @return \FOS\RestBundle\View\ViewHandler
     */
    private function getViewHandler()
    {
        return $this->container->get('fos_rest.view_handler');
    }

    /**
     * Forwards the action to the comment view on a successful form submission.
     *
     * @param string           $commentId     Id of the thread
     *
     * @return View
     */
    protected function onCreateFlagSuccess($commentId)
    {
        $threadId = $this->get('fos_comment.manager.comment')->findCommentById($commentId)->getThread()->getId();
        return RouteRedirectView::create('fos_comment_get_thread_comment', array('id' => $threadId, 'commentId' => $commentId));
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the form submission fails.
     *
     * @param FormInterface    $form   Form with the error
     * @param string           $id     Id of the thread
     *
     * @return View
     */
    protected function onCreateFlagError(FormInterface $form, $id)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                          'form' => $form,
                          'id' => $id,
                      ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Comment', 'flag_new'));

        return $view;
    }


}
