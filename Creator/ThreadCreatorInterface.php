<?php

namespace FOS\CommentBundle\Creator;

use FOS\CommentBundle\Model\ThreadInterface;

interface ThreadCreatorInterface
{
    /**
     * Creates a new thread with this identifier
     *
     * @param string $identifier
     * @return ThreadInterface
     */
    function create($identifier);
}
