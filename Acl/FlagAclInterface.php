<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\FlagInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 *
 * @author Hubert Bryłkowski <hubert@brylkowski.com>
 */
interface FlagAclInterface
{
    /**
     * Checks if the user should be allowed to create a flag.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be allowed to view a flag.
     *
     * @param FlagInterface $flag
     * @return bool
     */
    public function canView(FlagInterface $flag);

    /**
     * Checks if the user should be allowed to edit a flag.
     *
     * @param  FlagInterface $flag
     * @return boolean
     */
    public function canEdit(FlagInterface $flag);

    /**
     * Checks if the user should be allowed to delete a flag.
     *
     * @param  FlagInterface $flag
     * @return boolean
     */
    public function canDelete(FlagInterface $flag);

    /**
     * Sets the default Acl permissions on a flag.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new FlagInterface instances.
     *
     * @param  FlagInterface $flag
     * @return void
     */
    public function setDefaultAcl(FlagInterface $flag);

    /**
     * Installs the Default 'fallback' Acl entries for generic access.
     *
     * @return void
     */
    public function installFallbackAcl();

    /**
     * Removes default Acl entries
     *
     * @return void
     */
    public function uninstallFallbackAcl();
}
