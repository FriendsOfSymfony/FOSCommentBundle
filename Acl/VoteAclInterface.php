<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\VoteInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 *
 * Each function should throw an exception to be handled by the
 * Security system.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface VoteAclInterface
{
    /**
     * Checks if the user should be allowed to create a vote
     *
     * @throws Exception
     */
    function canCreate();

    /**
     * Checks if the user should be able to delete a vote
     *
     * @throws Exception
     */
    function canDelete(VoteInterface $vote);

    /**
     * Sets the default Acl permissions on a comment.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new CommentInterface instances.
     *
     * @param VoteInterface $comment
     * @return void
     */
    function setDefaultAcl(VoteInterface $vote);

    /**
     * Installs the Default 'fallback' Acl entries for generic access.
     *
     * @return void
     */
    function installFallbackAcl();

    /**
     * Removes default Acl entries
     *
     * @return void
     */
    function uninstallFallbackAcl();
}
