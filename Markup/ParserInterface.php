<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Markup;

/**
 * Interface to implement to bridge a Markup parser to
 * FOSCommentBundle.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface ParserInterface
{
    /**
     * Takes a markup string and returns raw html.
     *
     * @param  string $raw
     * @return string
     */
    public function parse($raw);
}
