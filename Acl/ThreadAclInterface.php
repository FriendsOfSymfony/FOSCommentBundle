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

use FOS\CommentBundle\Model\ThreadInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface ThreadAclInterface
{
    /**
     * Checks if the user should be allowed to create a thread.
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Checks if the user should be allowed to view a thread.
     *
     * @param  ThreadInterface $thread
     * @return boolean
     */
    public function canView(ThreadInterface $thread);

    /**
     * Checks if the user should be allowed to edit a thread.
     *
     * @param  ThreadInterface $thread
     * @return boolean
     */
    public function canEdit(ThreadInterface $thread);

    /**
     * Checks if the user should be allowed to delete a thread.
     *
     * @param  ThreadInterface $thread
     * @return boolean
     */
    public function canDelete(ThreadInterface $thread);

    /**
     * Sets the default Acl permissions on a thread.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new ThreadInterface instances.
     *
     * @param  ThreadInterface $thread
     * @return void
     */
    public function setDefaultAcl(ThreadInterface $thread);

    /**
     * Installs the Default 'fallback' Acl entries for generic access.
     *
     * @return void
     */
    public function installFallbackAcl();

    /**
     * Removes default Acl entries.
     *
     * @return void
     */
    public function uninstallFallbackAcl();
}
