Step 12b: Setup MongoDB mapping
==============================
The MongoDB implementation does not provide a concrete Vote class for your use,
you must create one:

``` php
<?php
// src/MyProject/MyBundle/Document/Vote.php

namespace MyProject\MyBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\CommentBundle\Document\Vote as BaseVote;

/**
 * @MongoDB\Document
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Vote extends BaseVote
{
    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * Comment of this vote
     *
     * @var Comment
     * @MongoDB\ReferenceOne(targetDocument="MyProject\MyBundle\Document\Comment")
     */
    protected $comment;
}
```

And you should implement `VotableCommentInterface` in your Comment class and add a field to your mapping:

``` php
<?php
// src/MyProject/MyBundle/Document/Comment.php

namespace MyProject\MyBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\CommentBundle\Document\Comment as BaseComment;
use FOS\CommentBundle\Model\VotableCommentInterface;

/**
 * @MongoDB\Document
 * @MongoDB\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Comment extends BaseComment implements VotableCommentInterface
{
    // .. fields

    /**
     * @MongoDB\Int
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

In YAML:

``` yaml
# app/config/config.yml

fos_comment:
    db_driver: mongodb
    class:
        model:
            vote: MyProject\MyBundle\Document\Vote
```

Or if you prefer XML:

``` xml
# app/config/config.xml

<fos_comment:config db-driver="mongodb">
    <fos_comment:class>
        <fos_comment:model
            vote="MyProject\MyBundle\Document\Vote"
        />
    </fos_comment:class>
</fos_comment:config>
```

### Back to the main step
[Step 12: Enable voting](12-enable_voting.md).
