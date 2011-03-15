<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

/**
 * A comment that implement this interface has an author name available
 */
interface SignedCommentInterface
{
    /**
     * @return string name of the author comment
     */
    function getAuthorName();
}
