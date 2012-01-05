<?php
namespace FOS\CommentBundle\Controller\Rest;

use FOS\CommentBundle\Model\CommentInterface,
    FOS\CommentBundle\Model\ThreadInterface;

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
            'id' => $form->getData()->getId()
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
     * Get the comments of a thread. Creates a new thread if none exists.
     *
     * @todo Add support page/pagesize/sorting/tree-depth parameters
     *
     * @param string $id
     * @return View
     */
    public function getThreadCommentsAction($id)
    {
        $displayDepth = $this->getRequest()->query->get('displayDepth');
        $sorter = $this->getRequest()->query->get('sorter');
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);

        // We're now sure it is no duplicate id, so create the thread
        if (null === $thread) {
            $thread = $this->container->get('fos_comment.manager.thread')
                ->createThreadFromQuery($id, $this->get('request')->query);

            // Add the thread
            $this->container->get('fos_comment.manager.thread')->addThread($thread);
        }

        $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread, $sorter, $displayDepth);

        $view = View::create()
          ->setStatusCode(200)
          ->setData(array('comments' => $comments, 'displayDepth' => $displayDepth, 'sorter' => 'date', 'thread' => $thread))
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread/rest', 'comments'));


        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Get a comment of a thread.
     *
     * @param string $id
     * @param mixed $commentId
     * @return View
     */
    public function getThreadCommentAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread
            || null === $comment
            || $comment->getThread() !== $thread
        ) {
            throw new NotFoundHttpException;
        }

        $view = View::create()
          ->setStatusCode(200)
          ->setData(array('comment' => $comment, 'thread' => $thread))
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread/rest', 'comment'));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Presents the form to use to create a new Comment for a Thread.
     *
     * @param string $id
     * @return View
     */
    public function newThreadCommentsAction($id)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (!$thread) {
            throw new NotFoundHttpException(sprintf('Thread with identifier of "%s" does not exist', $id));
        }

        $comment = $this->container->get('fos_comment.manager.comment')->createComment($thread);

        $parent = $this->getValidCommentParent($thread, $this->getRequest()->query->get('parentId'));

        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);

        $view = View::create()
          ->setStatusCode(200)
          ->setData(array(
              'form' => $form->createView(),
              'first' => 0 === $thread->getNumComments(),
              'thread' => $thread,
              'fos_comment_create_action_path' => $this->get('router')->generate('fos_comment_post_thread_comments', array('id' => $id, 'parentId' => $parent ? $parent->getId() : null)),
              'parent' => $parent,
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread/rest', 'comment_new'));

        return $view;
    }

    /**
     * Checks if a comment belongs to a thread. Returns the comment if it does.
     *
     * @param ThreadInterface $thread
     * @param mixed $commentId Id of the comment.
     *
     * @return CommentInterface The comment.
     */
    private function getValidCommentParent(ThreadInterface $thread, $commentId)
    {
        if(null !== $commentId) {
            $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);
            if (!$comment) {
                throw new NotFoundHttpException(sprintf('Parent comment with identifier "%s" does not exist', $commentId));
            }

            if($comment->getThread() !== $thread) {
                throw new NotFoundHttpException('Parent comment is not a comment of the given thread.');
            }

            return $comment;
        }
    }

    /**
     * Creates a new Comment for the Thread from the submitted data.
     *
     * @todo Add support for comment parent (in form?)
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

        $comment = $this->container
            ->get('fos_comment.manager.comment')
            ->createComment($thread, $this->getValidCommentParent($thread, $this->getRequest()->query->get('parentId')));

        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);
        $form->bindRequest($this->container->get('request'));

        if ($form->isValid() && $this->container->get('fos_comment.creator.comment')->create($comment)) {
            return $this->onCreateCommentSuccess($form);
        }

        return $this->onCreateCommentError($form);
    }

    /**
     * Forwards the action to the comment view on a successful form submission.
     *
     * @param CommentForm $form
     * @return Response
     */
    protected function onCreateCommentSuccess(Form $form)
    {
        return $this->container->get('http_kernel')->forward('FOSCommentBundle:Rest/Thread:getThreadComment', array(
            'id' => $form->getData()->getThread()->getId(),
            'commentId' => $form->getData()->getId(),
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
        $view = View::create()
          ->setStatusCode(400)
          ->setData(array(
              'form' => $form,
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread/rest', 'form_errors'));

        return $view;
    }

    /**
     * Presents the form to use to create a new Vote for a Comment.
     *
     * @param string id
     * @param mixed $commentId Id of the comment.
     * @return View
     */
    public function newThreadCommentVotesAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread
            || null === $comment
            || $comment->getThread() !== $thread
        ) {
            throw new NotFoundHttpException;
        }

        $vote = $this->container->get('fos_comment.manager.vote')->createVote();
        $vote->setValue($this->getRequest()->query->get('value', 1));

        $form = $this->container->get('fos_comment.form_factory.vote')->createForm();
        $form->setData($vote);

        $view = View::create()
          ->setStatusCode(200)
          ->setData(array(
              'id' => $id,
              'commentId' => $commentId,
              'form' => $form->createView()
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread/rest', 'vote_new'));

        return $view;
    }

    /**
     * Creates a new Vote for the Comment from the submitted data.
     *
     * @param string $id
     * @param mixed $commentId Id of the comment.
     * @return Response
     */
    public function postThreadCommentVotesAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread
            || null === $comment
            || $comment->getThread() !== $thread
        ) {
            throw new NotFoundHttpException;
        }

        $vote = $this->container->get('fos_comment.manager.vote')->createVote();

        $form = $this->container->get('fos_comment.form_factory.vote')->createForm();
        $form->setData($vote);

        $form->bindRequest($this->container->get('request'));

        if ($form->isValid() && $this->container->get('fos_comment.creator.vote')->create($vote, $comment)) {
            return $this->onCreateVoteSuccess($form);
        }

        return $this->onCreateVoteError($form);
    }

    /**
     * Action executed when a vote was succesfully created.
     *
     * @todo Think about what to show. For now the new score of the comment.
     *
     * @param VoteForm $form
     * @return Response
     */
    protected function onCreateVoteSuccess(Form $form)
    {
        $view = View::create()
          ->setStatusCode(200)
          ->setData(array(
              'commentScore' => $form->getData()->getComment()->getScore(),
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread/rest', 'vote_create_success'));

        return $view;
    }

    /**
     * Returns a 400 response when the form submission fails.
     *
     * @param VoteForm $form
     * @return Response
     */
    protected function onCreateVoteError(Form $form)
    {
        $view = View::create()
          ->setStatusCode(400)
          ->setData(array(
              'form' => $form,
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread/rest', 'form_errors'));

        return $view;
    }
}
