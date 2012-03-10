<?php

namespace FOS\CommentBundle\Tests\Functional\Bundle\CommentBundle\Entity;

use FOS\CommentBundle\Entity\Thread as BaseThread;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_thread")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 * @author Tim Nagel <tim@nagel.com.au>
 */
class Thread extends BaseThread
{
    /**
     * @var string $id
     *
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}