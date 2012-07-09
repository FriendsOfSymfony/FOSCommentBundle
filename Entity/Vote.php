<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Entity;

use FOS\CommentBundle\Model\Vote as BaseVote;

/**
 * Default ORM implementation of VoteInterface.
 *
 * This class must be extended and properly mapped by the developer.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
abstract class Vote extends BaseVote
{

}
