<?php

/**
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author Henrik Bjorn
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

/*
 * Binds a comment tree to anything, using a unique, arbitrary identifier
 */
interface ThreadInterface
{
    /**
     * Identifier, a unique string that binds the comments together in a thread (tree).
     * It can be a url or really anything unique.
     *
     * @return string
     */
    function getIdentifier();

    /**
     * @param string
     */
    function setIdentifier($identifier);

    /**
     * Tells if new comments can be added in this thread
     *
     * @return bool
     */
    function isCommentable();

    /**
     * @param bool $isCommentable
     */
    function setIsCommentable($isCommentable);

    /**
     * Gets the number of comments
     *
     * @return integer
     */
    function getNumComments();

    /**
     * Sets the number of comments
     *
     * @param integer $numComments
     */
    function setNumComments($numComments);
}
