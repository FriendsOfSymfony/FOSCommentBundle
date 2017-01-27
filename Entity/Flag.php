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

use FOS\CommentBundle\Model\Flag as AbstractFlag;

/**
 * Default ORM implementation of FlagInterface.
 *
 * This class must be extended and properly mapped by the developer.
 *
 * @author Hubert Bryłkowski <hubert@brylkowski.com>
 */
abstract class Flag extends AbstractFlag
{

}
