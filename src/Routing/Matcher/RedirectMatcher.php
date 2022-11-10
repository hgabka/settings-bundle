<?php

namespace Hgabka\SettingsBundle\Routing\Matcher;

use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Matcher\RedirectableUrlMatcher;

class RedirectMatcher extends RedirectableUrlMatcher
{
    /**
     * {@inheritdoc}
     */
    public function redirect(string $path, string $route, string $scheme = null): array
    {
        return [
            '_controller' => RedirectController::class . '::urlRedirectAction',
            'path' => $path,
            'permanent' => true,
            'scheme' => $scheme,
            'httpPort' => $this->context->getHttpPort(),
            'httpsPort' => $this->context->getHttpsPort(),
            '_route' => $route,
        ];
    }
}
