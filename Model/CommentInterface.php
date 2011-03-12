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
    function getId();

    /**
     * @return DateTime
     */
    function getCreatedAt();

    /**
     * @return DateTime
     */
    function getUpdatedAt();

    function incrementCreatedAt();

    function incrementUpdatedAt();
}
