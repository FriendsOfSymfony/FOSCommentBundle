<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\Controller;

/**
 * Determine which base class to use depending on what version of Symfony is installed.
 *
 * Symfony 3.3+ uses AbstractController, while older versions use Controller.
 */
if (class_exists('Symfony\Bundle\FrameworkBundle\Controller\AbstractController')) {
    class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
    {
    }
} else {
    class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\Controller
    {
    }
}
