<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Twig;

use FOS\CommentBundle\Model\VotableCommentInterface;

/**
 * Extends Twig to provide some helper functions for the CommentBundle.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class Extension extends \Twig_Extension
{
    public function getTests()
    {
        return array(
            'fos_comment_votable'        => new \Twig_Test_Method($this, 'isVotable'),
        );
    }

    public function getFunctions()
    {
        return array(
            'fos_comment_create_slug'   => new \Twig_Function_Method($this, 'createSlug'),
        );
    }

    /**
     * Checks if the comment is an instance of a VotableCommentInterface.
     *
     * @param mixed The value to check for VotableCommentInterface
     * @return bool If $value implements VotableCommentInterface
     */
    public function isVotable($value)
    {
        if (!is_object($value)) {
            return false;
        }

        return ($value instanceof VotableCommentInterface);
    }

    public function createSlug($value)
    {
        if (is_string($value)) {
            return $value;
        } else if (!is_object($value) || !method_exists($value, 'getId') || null === $value->getId()) {
            return null;
        }

        return sprintf('%s-%d', get_class($value), $value->getId());
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'fos_comment';
    }
}