<?php

/**
 * (c) Tim Nagel <tim@nagel.com.au>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Acl;

use FOS\CommentBundle\Model\CommentInterface;

/**
 * Used for checking if the ACL system will allow specific actions
 * to occur.
 *
 * Each function should throw an exception to be handled by the
 * Security system.
 *
 * @author Tim Nagel <tim@nagel.com.au>
 */
interface CommentAclInterface
{
    function canCreate();
    function canView(CommentInterface $comment);
    function canEdit(CommentInterface $comment);
    function canDelete(CommentInterface $comment);

    /**
     * Sets the default Acl permissions on a comment.
     *
     * Note: this does not remove any existing Acl and should only
     * be called on new CommentInterface instances.
     *
     * @param CommentInterface $comment
     * @return void
     */
    function setDefaultAcl(CommentInterface $comment);
}
