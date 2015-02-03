<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * A signed vote is bound to a FOS\UserBundle User model.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface SignedVoteInterface extends VoteInterface
{
    /**
     * Sets the owner of the vote
     *
     * @param UserInterface $voter
     */
    public function setVoter(UserInterface $voter);

    /**
     * Gets the owner of the vote
     *
     * @return UserInterface
     */
    public function getVoter();
}
