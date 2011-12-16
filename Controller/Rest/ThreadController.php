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
                    $this->onCreateThreadErrorDuplicate($form);
                }

                // Add the thread
                $this->container->get('fos_comment.manager.thread')->addThread($thread);

                $this->onCreateThreadSuccess($form);
            }
        }

        $this->onCreateThreadError($form);
    }

    /**
     * Forwards the action to the thread view on a successful form submission.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateThreadSuccess(Form $form)
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
    protected function onCreateThreadError(Form $form)
    {
        return new Response('An error occurred with form submission', 400);
    }

    /**
     * Returns a 400 response when the Thread creation fails due to a duplicate id.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateThreadErrorDuplicate(Form $form)
    {
        return new Response(sprintf("Duplicate thread id '%s'.", $form->getData()->getId()), 400);
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
     * Presents the form to use to create a new Comment for a Thread.
     *
     * @param string id
     * @return View
     */
    public function newThreadCommentsAction($id)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (!$thread) {
            throw new NotFoundHttpException(sprintf('Thread with identifier of "%s" does not exist', $id));
        }

        $comment = $this->container->get('fos_comment.manager.comment')->createComment($thread);

        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);

        $view = View::create()
          ->setStatusCode(200)
          ->setData(array(
              'form' => $form->createView(),
              'first' => 0 === $thread->getNumComments(),
              'thread' => $thread,
              'fos_comment_create_action_path' => $this->get('router')->generate('fos_comment_post_thread_comments', array('id' => $id)),
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Comment', 'new'));

        return $view;
    }

    /**
     * Creates a new Comment for the Thread from the submitted data.
     *
     * @todo Add support for comment parent (in form)
     *
     * @param string $id
     * @return Response
     */
    public function postThreadCommentsAction($id)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (!$thread) {
            throw new NotFoundHttpException(sprintf('Thread with identifier of "%s" does not exist', $id));
        }

        $comment = $this->container->get('fos_comment.manager.comment')->createComment($thread);

        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);
        $form->bindRequest($this->container->get('request'));

        if ($form->isValid() && $this->container->get('fos_comment.creator.comment')->create($comment)) {
            return $this->onCreateCommentSuccess($form);
        }

        return $this->onCreateCommentError($form);
    }

    /**
     * Forwards the action to the thread view on a successful form submission.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateCommentSuccess(Form $form)
    {
        return $this->container->get('http_kernel')->forward('FOSCommentBundle:Rest/Thread:getThreadComments', array(
            'id' => $form->getData()->getThread()->getId(),
            '_format' => 'json' // todo: change this when other formats are implemented
        ));
    }

    /**
     * Returns a 400 response when the form submission fails.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateCommentError(Form $form)
    {
        return new Response('An error occurred with form submission', 400);
    }
}
