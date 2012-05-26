<?php

/**
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . '/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;
use FOS\CommentBundle\Tests\Functional\AppKernel;

$kernel = new AppKernel('Behat', 'config.yml', 'test', true);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
