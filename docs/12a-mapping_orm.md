Step 12a: Setup Doctrine ORM mapping
===================================
The ORM implementation does not provide a concrete Vote class for your use,
you must create one. This can be done by extending the abstract entities
provided by the bundle and creating the appropriate mappings.

For example:

``` php
<?php
// src/MyProject/MyBundle/Entity/Vote.php

namespace MyProject\MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Vote as BaseVote;

/**
 * @ORM\Entity
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Vote extends BaseVote
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Comment of this vote
     *
     * @var Comment
     * @ORM\ManyToOne(targetEntity="MyProject\MyBundle\Entity\Comment")
     */
    protected $comment;
}
```

And you should implement `VotableCommentInterface` in your Comment class and add a field to your mapping:

``` php
<?php
// src/MyProject/MyBundle/Entity/Comment.php

namespace MyProject\MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\VotableCommentInterface;

/**
 * @ORM\Entity
 */
class Comment extends BaseComment implements VotableCommentInterface
{
    // .. fields

    /**
     * @ORM\Column(type="integer")
     * @var int
     */
    protected $score = 0;

    /**
     * Sets the score of the comment.
     *
     * @param integer $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * Returns the current score of the comment.
     *
     * @return integer
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Increments the comment score by the provided
     * value.
     *
     * @param integer value
     *
     * @return integer The new comment score
     */
    public function incrementScore($by = 1)
    {
        $this->score += $by;
    }

}
```

## Configure your application

``` yaml
# app/config/config.yml

fos_comment:
    db_driver: orm
    class:
        model:
            vote: MyProject\MyBundle\Entity\Vote
```

Or if you prefer XML:

``` xml
# app/config/config.xml

<fos_comment:config db-driver="orm">
    <fos_comment:class>
        <fos_comment:model
            vote="MyProject\MyBundle\Entity\Vote"
        />
    </fos_comment:class>
</fos_comment:config>
```
### Back to the main step
[Step 12: Enable voting](12-enable_voting.md).
