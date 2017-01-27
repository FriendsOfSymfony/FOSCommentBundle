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
 * A signed flag is bound to a FOS\UserBundle User model.
 *
 * @author Hubert Bryłkowski <hubert@brylkowski.com>
 */
interface SignedFlagInterface extends FlagInterface
{
    /**
     * Sets the owner of the vote
     *
     * @param UserInterface $voter
     */
    public function setFlagger(UserInterface $voter);

    /**
     * Gets the owner of the flag
     *
     * @return UserInterface
     */
    public function getFlagger();
}
