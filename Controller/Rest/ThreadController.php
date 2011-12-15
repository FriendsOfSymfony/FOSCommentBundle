<?php
namespace FOS\CommentBundle\Controller\Rest;

use FOS\RestBundle\View\View;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Bundle\FrameworkBundle\Templating\TemplateReference,
    Symfony\Component\Form\Form,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Restful controller for the Threads.
 *
 * @uses Controller
 */
class ThreadController extends Controller
{
    /**
     * Gets the thread for a given id.
     *
     * @param string $id
     * @return View
     */
    public function getThreadAction($id)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);

        if (null === $thread) {
            throw new NotFoundHttpException;
        }

        $view = View::create()
          ->setStatusCode(200)
          ->setData(array('thread' => $thread));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Get the comments of a thread.
     *
     * @todo Add support page/pagesize/sorting/tree-depth parameters
     *
     * @param string $id
     * @return View
     */
    public function getThreadCommentsAction($id)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread);

        $view = View::create()
          ->setStatusCode(200)
          ->setData(array('comments' => $comments));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Presents the form to use to create a new Thread.
     *
     * @return View
     */
    public function newThreadsAction()
    {
        $form = $this->container->get('fos_comment.form_factory.thread')->createForm();

        $view = View::create()
          ->setStatusCode(200)
          ->setData(array('form' => $form->createView()))
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'new'));

        return $view;
    }

    /**
     * Creates a new Thread from the submitted data.
     *
     * @return Response
     */
    public function postThreadsAction()
    {
        $thread = $this->container->get('fos_comment.manager.thread')->createThread();
        $form = $this->container->get('fos_comment.form_factory.thread')->createForm();
        $form->setData($thread);

        $request = $this->container->get('request');
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                if (null !== $this->container->get('fos_comment.manager.thread')->findThreadById($thread->getId())) {
                    $this->onCreateErrorDuplicate($form);
                }

                // Add the thread
                $this->container->get('fos_comment.manager.thread')->addThread($thread);

                $this->onCreateSuccess($form);
            }
        }

        $this->onCreateError($form);
    }

    /**
     * Forwards the action to the thread view on a successful form submission.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateSuccess(Form $form)
    {
        return $this->container->get('http_kernel')->forward('FOSCommentBundle:Rest/Thread:getThread', array(
            'id' => $form->getData()->getId(),
            '_format' => 'json' // todo: change this when other formats are implemented
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
        return new Response('An error occurred with form submission', 400);
    }

    /**
     * Returns a 400 response when the Thread creation fails due to a duplicate id.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateErrorDuplicate(Form $form)
    {
        return new Response(sprintf("Duplicate thread id '%s'.", $form->getData()->getId()), 400);
    }
}
