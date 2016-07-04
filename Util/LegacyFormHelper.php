<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FOS\CommentBundle\Util;

/**
 * Extracted from FOSUserBundle
 *
 * @internal
 *
 * @author Gabor Egyed <gabor.egyed@gmail.com>
 */
final class LegacyFormHelper
{
    private static $map = array(
        'FOS\CommentBundle\Form\CommentableThreadType' => 'fos_comment_commentable_thread',
        'FOS\CommentBundle\Form\CommentType' => 'fos_comment_comment',
        'FOS\CommentBundle\Form\DeleteCommentType' => 'fos_comment_delete_comment',
        'FOS\CommentBundle\Form\ThreadType' => 'fos_comment_thread',
        'FOS\CommentBundle\Form\VoteType' => 'fos_comment_vote',
        'Symfony\Component\Form\Extension\Core\Type\TextType' => 'text',
        'Symfony\Component\Form\Extension\Core\Type\HiddenType' => 'hidden',
        'Symfony\Component\Form\Extension\Core\Type\TextareaType' => 'textarea',
    );

    /**
     * @param string $class
     *
     * @return string|null
     */
    public static function getType($class)
    {
        if (!self::isLegacy()) {
            return $class;
        }

        if (!isset(self::$map[$class])) {
            throw new \InvalidArgumentException(sprintf('Form type with class "%s" can not be found. Please check for typos or add it to the map in LegacyFormHelper', $class));
        }

        return self::$map[$class];
    }

    /**
     * @return bool
     */
    public static function isLegacy()
    {
        return !method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix');
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}
