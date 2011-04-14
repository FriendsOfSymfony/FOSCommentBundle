<?php

/**
 * This file is part of the FOS\CommentBundle.
 *
 * (c) Thibault Duplessis <thibault.duplessis@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Form\ValueTransformer;

use FOS\CommentBundle\Model\ThreadInterface;
use FOS\CommentBundle\Model\ThreadManagerInterface;
use Symfony\Component\Form\Configurable;
use Symfony\Component\Form\ValueTransformer\ValueTransformerInterface;

/**
 * Transforms between a thread object and an identifier string
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
class ThreadValueTransformer extends Configurable implements ValueTransformerInterface
{
    /**
     * Thread manager
     *
     * @var ThreadManagerInterface
     */
    protected $manager = null;

    /**
     * Constructor.
     *
     * @param ThreadManagerInterface $threadManager
     */
    public function __construct(ThreadManagerInterface $threadManager)
    {
        $this->manager = $threadManager;
    }

    /**
     * Transforms an object into an id
     *
     * @param  mixed $value     Object
     * @return string           String id
     */
    public function transform($thread)
    {
        if(null === $thread) {
            return null;
        }

        if (!$thread instanceof ThreadInterface) {
            return null;
        }

        return $thread->getIdentifier();
    }

    /**
     * Transforms an id into a ThreadInterface, if one exists
     *
     * @param mixed $identifier
     * @return ThreadInterface
     */
    public function reverseTransform($identifier)
    {
        return $this->manager->findThreadByIdentifier($identifier);
    }
}
