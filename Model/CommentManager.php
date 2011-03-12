<?php

namespace FOS\CommentBundle\Model;

/**
 * Abstract Comment Manager implementation which can be used as base class for your
 * concrete manager.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
abstract class CommentManager implements CommentManagerInterface, CommentProviderInterface
{
    /*
     * Common, strategy agnostic method to get all nested comments.
     * Will typically be used when it comes to display the comments.
     *
     * @param  string $identifier
     * @return array(
     *     'comment' => CommentInterface,
     *     'children' => array(
     *         0 => array (
     *             'comment' => CommentInterface,
     *             'children' => array(...)
     *         ),
     *         1 => array (
     *             'comment' => CommentInterface,
     *             'children' => array(...)
     *         )
     *     )
     */
    function findCommentsByThread(CommentThreadInterface $thread)
    {
        throw new Exception('Not implemented.');
    }

    /**
     * Returns an empty comment instance
     *
     * @return Comment
     */
    public function createComment()
    {
        $class = $this->getClass();
        $comment = new $class;

        return $comment;
    }
}
