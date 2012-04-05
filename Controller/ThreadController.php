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
use FOS\Rest\Util\Codes;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Restful controller for the Threads.
 *
 * @author Alexander <iam.asm89@gmail.com>
 */
class ThreadController extends Controller
{
    const VIEW_FLAT = 'flat';
    const VIEW_TREE = 'tree';

    /**
     * Presents the form to use to create a new Thread.
     *
     * @return View
     */
    public function newThreadsAction()
    {
        $form = $this->container->get('fos_comment.form_factory.thread')->createForm();

        $view = View::create()
            ->setData(array('form' => $form->createView()))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'new'));

        return $view;
    }

    /**
     * Gets the thread for a given id.
     *
     * @param string $id
     *
     * @return View
     */
    public function getThreadAction($id)
    {
        $manager = $this->container->get('fos_comment.manager.thread');
        $thread = $manager->findThreadById($id);

        if (null === $thread) {
            throw new NotFoundHttpException(sprintf("Thread with id '%s' could not be found.", $id));
        }

        $view = View::create()
            ->setData(array('thread' => $thread));

        return $view;
    }

    /**
     * Gets the threads for the specified ids.
     *
     * @param Request $request
     *
     * @return View
     */
    public function getThreadsActions(Request $request)
    {
        $ids = $request->query->get('ids');

        $threads = $this->container->get('fos_comment.manager.thread')->findThreadsBy(array('id' => $ids));

        $view = View::create()
            ->setData(array('threads' => $threads));

        return $view;
    }

    /**
     * Creates a new Thread from the submitted data.
     *
     * @return View
     */
    public function postThreadsAction()
    {
        $threadManager = $this->container->get('fos_comment.manager.thread');
        $thread = $threadManager->createThread();
        $form = $this->container->get('fos_comment.form_factory.thread')->createForm();
        $form->setData($thread);

        $request = $this->container->get('request');
        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                if (null !== $threadManager->findThreadById($thread->getId())) {
                    $this->onCreateThreadErrorDuplicate($form);
                }

                // Add the thread
                $threadManager->saveThread($thread);

                return $this->onCreateThreadSuccess($form);
            }
        }

        return $this->onCreateThreadError($form);
    }

    /**
     * Presents the form to use to create a new Comment for a Thread.
     *
     * @param string $id
     *
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
            ->setData(array(
                'form' => $form->createView(),
                'first' => 0 === $thread->getNumComments(),
                'thread' => $thread,
                'parent' => $parent,
                'id' => $id,
                'parent' => $parent,
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_new'));

        return $view;
    }

    /**
     * Get a comment of a thread.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function getThreadCommentAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $view = View::create()
            ->setData(array('comment' => $comment, 'thread' => $thread))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment'));

        return $view;
    }

    /**
     * Get the comments of a thread. Creates a new thread if none exists.
     *
     * @param Request $request Current request
     * @param string  $id      Id of the thread
     *
     * @return View
     * @todo Add support page/pagesize/sorting/tree-depth parameters
     */
    public function getThreadCommentsAction(Request $request, $id)
    {
        $displayDepth = $request->query->get('displayDepth');
        $sorter = $request->query->get('sorter');
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);

        // We're now sure it is no duplicate id, so create the thread
        if (null === $thread) {
            $thread = $this->container->get('fos_comment.manager.thread')
                ->createThread();
            $thread->setId($id);
            $thread->setPermalink($request->query->get('permalink'));

            // Add the thread
            $this->container->get('fos_comment.manager.thread')->saveThread($thread);
        }

        $viewMode = $request->query->get('view', 'tree');
        switch ($viewMode) {
            case self::VIEW_FLAT:
                $comments = $this->container->get('fos_comment.manager.comment')->findCommentsByThread($thread, $displayDepth, $sorter);

                // We need nodes for the api to return a consistent response, not an array of comments
                $comments = array_map(function($comment) {
                        return array('comment' => $comment, 'children' => array());
                    },
                    $comments
                );
                break;
            case self::VIEW_TREE:
            default:
                $comments = $this->container->get('fos_comment.manager.comment')->findCommentTreeByThread($thread, $sorter, $displayDepth);
                break;
        }

        $view = View::create()
            ->setData(array(
                'comments' => $comments,
                'displayDepth' => $displayDepth,
                'sorter' => 'date',
                'thread' => $thread,
                'view' => $viewMode,
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comments'));

        // Register a special handler for RSS. Only available on this route.
        if ('rss' === $request->getRequestFormat()) {
            $templatingHandler = function($handler, $view, $request) {
                $view->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'thread_xml_feed'));

                return new Response($handler->renderTemplate($view, 'rss'), Codes::HTTP_OK, $view->getHeaders());
            };

            $this->get('fos_rest.view_handler')->registerHandler('rss', $templatingHandler);
        }

        return $view;
    }

    /**
     * Creates a new Comment for the Thread from the submitted data.
     *
     * @param Request $request The current request
     * @param string  $id      The id of the thread
     *
     * @return View
     * @todo Add support for comment parent (in form?)
     */
    public function postThreadCommentsAction(Request $request, $id)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        if (!$thread) {
            throw new NotFoundHttpException(sprintf('Thread with identifier of "%s" does not exist', $id));
        }

        $parent = $this->getValidCommentParent($thread, $request->query->get('parentId'));
        $commentManager = $this->container->get('fos_comment.manager.comment');
        $comment = $commentManager->createComment($thread, $parent);

        $form = $this->container->get('fos_comment.form_factory.comment')->createForm();
        $form->setData($comment);
        $form->bindRequest($this->container->get('request'));

        if ($form->isValid()) {
            $commentManager->saveComment($comment);;

            return $this->onCreateCommentSuccess($form, $id, $parent);
        }

        return $this->onCreateCommentError($form, $id, $parent);
    }

    /**
     * Get the votes of a comment.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function getThreadCommentVotesAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $view = View::create()
            ->setData(array(
                'commentScore' => $comment->getScore(),
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_votes'));

        return $view;
    }

    /**
     * Presents the form to use to create a new Vote for a Comment.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function newThreadCommentVotesAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $vote = $this->container->get('fos_comment.manager.vote')->createVote($comment);
        $vote->setValue($this->getRequest()->query->get('value', 1));

        $form = $this->container->get('fos_comment.form_factory.vote')->createForm();
        $form->setData($vote);

        $view = View::create()
            ->setData(array(
                'id' => $id,
                'commentId' => $commentId,
                'form' => $form->createView()
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'vote_new'));

        return $view;
    }

    /**
     * Creates a new Vote for the Comment from the submitted data.
     *
     * @param string $id        Id of the thread
     * @param mixed  $commentId Id of the comment
     *
     * @return View
     */
    public function postThreadCommentVotesAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $voteManager = $this->container->get('fos_comment.manager.vote');
        $vote = $voteManager->createVote($comment);

        $form = $this->container->get('fos_comment.form_factory.vote')->createForm();
        $form->setData($vote);

        $form->bindRequest($this->container->get('request'));

        if ($form->isValid()) {
            $voteManager->saveVote($vote);

            return $this->onCreateVoteSuccess($form, $id, $commentId);
        }

        return $this->onCreateVoteError($form, $id, $commentId);
    }

    /**
     * Forwards the action to the comment view on a successful form submission.
     *
     * @param FormInterface    $form   Form with the error
     * @param string           $id     Id of the thread
     * @param CommentInterface $parent Optional comment parent
     *
     * @return View
     */
    protected function onCreateCommentSuccess(FormInterface $form, $id, CommentInterface $parent = null)
    {
        return RouteRedirectView::create('fos_comment_get_thread_comment', array('id' => $id, 'commentId' => $form->getData()->getId()));
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the form submission fails.
     *
     * @param FormInterface    $form   Form with the error
     * @param string           $id     Id of the thread
     * @param CommentInterface $parent Optional comment parent
     *
     * @return View
     */
    protected function onCreateCommentError(FormInterface $form, $id, CommentInterface $parent = null)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                'form' => $form,
                'id' => $id,
                'parent' => $parent,
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_new'));

        return $view;
    }

    /**
     * Forwards the action to the thread view on a successful form submission.
     *
     * @param FormInterface $form
     *
     * @return View
     */
    protected function onCreateThreadSuccess(FormInterface $form)
    {
        return RouteRedirectView::create('fos_comment_get_thread', array('id' => $form->getData()->getId()));
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the form submission fails.
     *
     * @param FormInterface $form
     *
     * @return View
     */
    protected function onCreateThreadError(FormInterface $form)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                'form' => $form,
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'new'));

        return $view;
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the Thread creation fails due to a duplicate id.
     *
     * @param FormInterface $form
     *
     * @return View
     */
    protected function onCreateThreadErrorDuplicate(FormInterface $form)
    {
        return new Response(sprintf("Duplicate thread id '%s'.", $form->getData()->getId()), Codes::HTTP_BAD_REQUEST);
    }

    /**
     * Action executed when a vote was succesfully created.
     *
     * @param FormInterface $form      Form with the error
     * @param string        $id        Id of the thread
     * @param mixed         $commentId Id of the comment
     *
     * @return View
     * @todo Think about what to show. For now the new score of the comment.
     */
    protected function onCreateVoteSuccess(FormInterface $form, $id, $commentId)
    {
        return RouteRedirectView::create('fos_comment_get_thread_comment_votes', array('id' => $id, 'commentId' => $commentId));
    }

    /**
     * Returns a HTTP_BAD_REQUEST response when the form submission fails.
     *
     * @param FormInterface $form      Form with the error
     * @param string        $id        Id of the thread
     * @param mixed         $commentId Id of the comment
     *
     * @return View
     */
    protected function onCreateVoteError(FormInterface $form, $id, $commentId)
    {
        $view = View::create()
            ->setStatusCode(Codes::HTTP_BAD_REQUEST)
            ->setData(array(
                'id' => $id,
                'commentId' => $commentId,
                'form' => $form,
            ))
            ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'vote_new'));

        return $view;
    }

    /**
     * Checks if a comment belongs to a thread. Returns the comment if it does.
     *
     * @param ThreadInterface $thread    Thread object
     * @param mixed           $commentId Id of the comment.
     *
     * @return CommentInterface|null The comment.
     */
    private function getValidCommentParent(ThreadInterface $thread, $commentId)
    {
        if (null !== $commentId) {
            $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);
            if (!$comment) {
                throw new NotFoundHttpException(sprintf('Parent comment with identifier "%s" does not exist', $commentId));
            }

            if ($comment->getThread() !== $thread) {
                throw new NotFoundHttpException('Parent comment is not a comment of the given thread.');
            }

            return $comment;
        }
    }
}
