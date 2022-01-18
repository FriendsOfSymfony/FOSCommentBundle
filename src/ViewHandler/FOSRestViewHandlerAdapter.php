<?php

/*
 * This file is part of the FOSCommentBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace FOS\CommentBundle\ViewHandler;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class FOSRestViewHandlerAdapter implements ViewHandlerInterface
{
    /**
     * @var ViewHandlerInterface
     */
    private $decorated;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(ViewHandlerInterface $decorated, Environment $twig, RequestStack $requestStack)
    {
        $this->decorated = $decorated;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
    }

    public function supports($format): bool
    {
        return $this->decorated->supports($format);
    }

    public function registerHandler($format, $callable): void
    {
        $this->decorated->registerHandler($format, $callable);
    }

    public function handle(View $view, Request $request = null): Response
    {
        $data = $view->getData();

        if (null === $request) {
            $request = $this->requestStack->getCurrentRequest();
        }

        if ('html' === ($view->getFormat() ?: $request->getRequestFormat()) && is_array($data)) {
            $template = $data['template'];
            $templateData = $data['data'];

            $response = $this->twig->render($template, $templateData);

            return new Response($response);
        }

        if (is_array($data)) {
            $view->setData($data['data'] ?? $data);
        }

        return $this->decorated->handle($view, $request);
    }

    public function createRedirectResponse(View $view, $location, $format): Response
    {
        return $this->decorated->createRedirectResponse($view, $location, $format);
    }

    public function createResponse(View $view, Request $request, $format): Response
    {
        return $this->decorated->createResponse($view, $request, $format);
    }

    /**
     * @deprecated
     */
    public function isFormatTemplating($format)
    {
        return $this->decorated->isFormatTemplating($format);
    }

    /**
     * @deprecated
     */
    public function renderTemplate(View $view, $format)
    {
        return $this->decorated->renderTemplate($view, $format);
    }

    /**
     * @deprecated
     */
    public function prepareTemplateParameters(View $view)
    {
        return $this->decorated->prepareTemplateParameters($view);
    }
}
