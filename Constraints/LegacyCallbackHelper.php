<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Constraints;

use Symfony\Component\Validator\Constraints\Callback;

/**
 * BC helper for {@link Model\Vote::isValid()}.
 *
 * @internal do not use this class.
 */
final class LegacyCallbackHelper extends Callback
{
    public function __construct($options = null)
    {
        if($options['value'] == 'isValid' && !class_exists('Symfony\Component\Validator\Context\ExecutionContextInterface')) {
            $options['value'] = 'isVoteValid';
        }

        parent::__construct();
    }

    public function validatedBy()
    {
        return 'Symfony\Component\Validator\Constraints\CallbackValidator';
    }
}
