Step 6: Integration with FOSUserBundle
======================================
By default, comments are made anonymously.
[FOSUserBundle](http://github.com/FriendsOfSymfony/FOSUserBundle)
authentication can be used to sign the comments.

### A) Setup FOSUserBundle
First you have to setup [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle). Check the [instructions](https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/index.md).

### B) Extend the Comment class
In order to add an author to a comment, the Comment class should implement the
`SignedCommentInterface` and add a field to your mapping.

For example in the ORM:

``` php
<?php
// src/MyProject/MyBundle/Entity/Comment.php

namespace MyProject\MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Comment as BaseComment;
use FOS\CommentBundle\Model\SignedCommentInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 */
class Comment extends BaseComment implements SignedCommentInterface
{
    // .. fields

    /**
     * Author of the comment
     *
     * @ORM\ManyToOne(targetEntity="MyProject\MyBundle\Entity\User")
     * @var User
     */
    protected $author;

    public function setAuthor(UserInterface $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function getAuthorName()
    {
        if (null === $this->getAuthor()) {
            return 'Anonymous';
        }

        return $this->getAuthor()->getUsername();
    }
}
```

## That is it!
[Return to the index.](index.md)
