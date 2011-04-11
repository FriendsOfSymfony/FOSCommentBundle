<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
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
 * Each function should throw an exception to be handled by the
 * Security system.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface ThreadAclInterface
{
    /**
     * Checks if the user should be allowed to create a thread.
     *
     * @throws Exception
     */
    function canCreate();

    /**
     * Checks if the user should be allowed to view a thread.
     *
     * @throws Exception
     * @param ThreadInterface $thread
     */
    function canView(ThreadInterface $thread);

    /**
     * Checks if the user should be allowed to edit a thread.
     *
     * @throws Exception
     * @param ThreadInterface $thread
     */
    function canEdit(ThreadInterface $thread);

    /**
     * Checks if the user should be allowed to delete a thread.
     *
     * @throws Exception
     * @param ThreadInterface $thread
     */
    function canDelete(ThreadInterface $thread);

    /**
     * Sets the default Acl permissions on a thread.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new ThreadInterface instances.
     *
     * @param ThreadInterface $thread
     * @return void
     */
    function setDefaultAcl(ThreadInterface $thread);

    /**
     * Installs the Default 'fallback' Acl entries for generic access.
     *
     * @return void
     */
    function installFallbackAcl();

    /**
     * Removes default Acl entries.
     *
     * @return void
     */
    function uninstallFallbackAcl();
}
