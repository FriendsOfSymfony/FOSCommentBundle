<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Tests\Functional\Bundle\CommentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Flag as BaseFlag;

/**
 * @ORM\Entity
 * @ORM\Table(name="test_flag")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Flag extends BaseFlag
{
    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Comment")
     * @var Comment
     */
    protected $comment;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @var string
     */
    protected $reason;

}
