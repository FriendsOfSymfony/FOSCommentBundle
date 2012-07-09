<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

/**
 * A comment that holds a raw version of the comment allowing
 * for different markup languages to be used.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface RawCommentInterface extends CommentInterface
{
    /**
     * Gets the raw processed html.
     *
     * @return string
     */
    public function getRawBody();

    /**
     * Sets the processed body with raw html.
     *
     * @param string $rawBody
     */
    public function setRawBody($rawBody);
}
