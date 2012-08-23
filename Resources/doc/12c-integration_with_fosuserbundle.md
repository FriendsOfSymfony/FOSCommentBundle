Step 12c: Integration with FOSUserBundle
======================================
By default, votes are made anonymously.
[FOSUserBundle](http://github.com/FriendsOfSymfony/FOSUserBundle)
authentication can be used to sign the votes.

### A) Setup FOSUserBundle
First you have to setup [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle). Check the [instructions](https://github.com/FriendsOfSymfony/FOSUserBundle/blob/master/Resources/doc/index.md).

### B) Extend the Vote class
In order to add an author to a vote, the Vote class should implement the
`SignedVoteInterface` and add a field to your mapping.

For example in the ORM:

``` php
<?php
// src/MyProject/MyBundle/Entity/Vote.php

namespace MyProject\MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\CommentBundle\Entity\Vote as BaseVote;
use FOS\CommentBundle\Model\SignedVoteInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 */
class Vote extends BaseVote implements SignedVoteInterface
{
    // .. fields

    /**
     * Author of the vote
     *
     * @ORM\ManyToOne(targetEntity="MyProject\MyBundle\Entity\User")
     * @var User
     */
    protected $voter;

    /**
     * Sets the owner of the vote
     *
     * @param string $user
     */
    public function setVoter(UserInterface $voter)
    {
        $this->voter = $voter;
    }

    /**
     * Gets the owner of the vote
     *
     * @return UserInterface
     */
    public function getVoter()
    {
        return $this->voter;
    }
}
```

## That is it!
[Return to the index.](index.md)
