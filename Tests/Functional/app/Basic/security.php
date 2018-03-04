<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Controller\UserValueResolver;

if (method_exists(Security::class, 'getUser') && !class_exists(UserValueResolver::class)) {
    $container->loadFromExtension('security', array(
        'firewalls' => array(
            'main' => array(
                'logout_on_user_change' => true,
            ),
        ),
    ));
}
