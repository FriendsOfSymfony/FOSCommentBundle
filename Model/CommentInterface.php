<?php

/**
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

interface CommentInterface
{
    /**
     * @return string
     */
    function getBody();

    /**
     * @param string $body
     */
    function setBody($body);

    /**
     * @return DateTime
     */
    function getCreatedAt();
}
