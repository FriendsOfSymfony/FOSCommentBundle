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
 * Uses HTMLPurifier to parse and sanitise html.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
class HtmlPurifier implements ParserInterface
{
    private $purifier;

    public function __construct(\HTMLPurifier $purifier)
    {
        $this->purifier = $purifier;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($raw)
    {
        return $this->purifier->purify($raw);
    }
}
