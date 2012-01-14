<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle;

final class Events
{
    const COMMENT_PRE_PERSIST = 'fos_comment.comment.pre_persist';
    const COMMENT_POST_PERSIST = 'fos_comment.comment.post_persist';

    const COMMENT_CREATE = 'fos_comment.comment.create';

    const THREAD_PRE_PERSIST = 'fos_comment.thread.pre_persist';
    const THREAD_POST_PERSIST = 'fos_comment.thread.post_persist';

    const THREAD_CREATE = 'fos_comment.thread.create';

    const VOTE_PRE_PERSIST = 'fos_comment.vote.pre_persist';
    const VOTE_POST_PERSIST = 'fos_comment.vote.post_persist';

    const VOTE_CREATE = 'fos_comment.vote.create';
}