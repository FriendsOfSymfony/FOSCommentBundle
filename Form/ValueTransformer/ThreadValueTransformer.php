<?php

namespace FOS\CommentBundle\Form\ValueTransformer;

use Symfony\Component\Form\ValueTransformer\ValueTransformerInterface;
use Symfony\Component\Form\Configurable;

use FOS\CommentBundle\Model\ThreadManagerInterface;

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

        return $thread->getIdentifier();
    }

    public function reverseTransform($identifier)
    {
        return $this->manager->findThreadByIdentifier($identifier);
    }
}
