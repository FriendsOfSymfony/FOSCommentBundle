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
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Form\Form;
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
          ->setStatusCode(200)
          ->setData(array('form' => $form->createView()))
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'new'));

        return $view;
    }

    /**
     * Gets the thread for a given id.
     *
     * @param string $id
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
          ->setStatusCode(200)
          ->setData(array('thread' => $thread));

        return $view;
    }

    /**
     * Creates a new Thread from the submitted data.
     *
     * @return View
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

                return $this->onCreateThreadSuccess($form);
            }
        }

        return $this->onCreateThreadError($form);
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
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment_new'));

        return $view;
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

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
        }

        $view = View::create()
          ->setStatusCode(200)
          ->setData(array('comment' => $comment, 'thread' => $thread))
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comment'));

        return $view;
    }

    /**
     * Get the comments of a thread. Creates a new thread if none exists.
     *
     * @todo Add support page/pagesize/sorting/tree-depth parameters
     *
     * @param Request $request
     * @param string $id
     * @return View
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
            $this->container->get('fos_comment.manager.thread')->addThread($thread);
        }

        $view = $request->query->get('view', 'tree');
        switch($view) {
            case self::VIEW_FLAT:
                $comments = $this->container->get('fos_comment.manager.comment')->findCommentsByThread($thread, $sorter, $displayDepth);

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
          ->setStatusCode(200)
          ->setData(array(
              'comments' => $comments,
              'displayDepth' => $displayDepth,
              'sorter' => 'date',
              'thread' => $thread,
              'view' => $view,
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'comments'));

        // Register a special handler for RSS. Only available on this route.
        if('rss' === $request->getRequestFormat()) {
            $templatingHandler = function($handler, $view, $request) {
                $view->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'thread_xml_feed'));

                return new Response($handler->renderTemplate($view, 'rss'), 200, $view->getHeaders());
            };

            $this->get('fos_rest.view_handler')->registerHandler('rss', $templatingHandler);
        }

        return $view;
    }

    /**
     * Creates a new Comment for the Thread from the submitted data.
     *
     * @todo Add support for comment parent (in form?)
     *
     * @param string $id
     * @return View
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

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
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
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'vote_new'));

        return $view;
    }

    /**
     * Creates a new Vote for the Comment from the submitted data.
     *
     * @param string $id
     * @param mixed $commentId Id of the comment.
     * @return View
     */
    public function postThreadCommentVotesAction($id, $commentId)
    {
        $thread = $this->container->get('fos_comment.manager.thread')->findThreadById($id);
        $comment = $this->container->get('fos_comment.manager.comment')->findCommentById($commentId);

        if (null === $thread || null === $comment || $comment->getThread() !== $thread) {
            throw new NotFoundHttpException(sprintf("No comment with id '%s' found for thread with id '%s'", $commentId, $id));
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
     * Forwards the action to the comment view on a successful form submission.
     *
     * @param CommentForm $form
     * @return View
     */
    protected function onCreateCommentSuccess(Form $form)
    {
        return $this->getThreadCommentAction($form->getData()->getThread()->getId(), $form->getData()->getId());
    }

    /**
     * Returns a 400 response when the form submission fails.
     *
     * @param CommentForm $form
     * @return View
     */
    protected function onCreateCommentError(Form $form)
    {
        $view = View::create()
          ->setStatusCode(400)
          ->setData(array(
              'form' => $form,
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'form_errors'));

        return $view;
    }

    /**
     * Forwards the action to the thread view on a successful form submission.
     *
     * @param CommentForm $form
     * @return View
     */
    protected function onCreateThreadSuccess(Form $form)
    {
        return $this->getThreadAction($form->getData()->getId());
    }

    /**
     * Returns a 400 response when the form submission fails.
     *
     * @param CommentForm $form
     * @return View
     */
    protected function onCreateThreadError(Form $form)
    {
        $view = View::create()
          ->setStatusCode(400)
          ->setData(array(
              'form' => $form,
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'form_errors'));

        return $view;
    }

    /**
     * Returns a 400 response when the Thread creation fails due to a duplicate id.
     *
     * @param CommentForm $form
     * @return View
     */
    protected function onCreateThreadErrorDuplicate(Form $form)
    {
        return new Response(sprintf("Duplicate thread id '%s'.", $form->getData()->getId()), 400);
    }

    /**
     * Action executed when a vote was succesfully created.
     *
     * @todo Think about what to show. For now the new score of the comment.
     *
     * @param VoteForm $form
     * @return View
     */
    protected function onCreateVoteSuccess(Form $form)
    {
        $view = View::create()
          ->setStatusCode(200)
          ->setData(array(
              'commentScore' => $form->getData()->getComment()->getScore(),
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'vote_create_success'));

        return $view;
    }

    /**
     * Returns a 400 response when the form submission fails.
     *
     * @param VoteForm $form
     * @return View
     */
    protected function onCreateVoteError(Form $form)
    {
        $view = View::create()
          ->setStatusCode(400)
          ->setData(array(
              'form' => $form,
              )
          )
          ->setTemplate(new TemplateReference('FOSCommentBundle', 'Thread', 'form_errors'));

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
}
