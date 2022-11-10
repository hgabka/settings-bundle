<?php

namespace Hgabka\SettingsBundle\Router;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Hgabka\SettingsBundle\Entity\Redirect;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

class RedirectRouter implements RouterInterface
{
    /** @var RequestContext */
    private $context;

    /** @var RouteCollection */
    private $routeCollection;

    /** @var ObjectRepository */
    private $redirectRepository;

    /**
     * @param ObjectRepository $redirectRepository
     */
    public function __construct(EntityRepository $redirectRepository)
    {
        $this->redirectRepository = $redirectRepository;
        $this->context = new RequestContext();
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        throw new RouteNotFoundException('You cannot generate a url from a redirect');
    }

    public function match(string $pathinfo): array
    {
        $urlMatcher = new UrlMatcher($this->getRouteCollection(), $this->getContext());
        $result = $urlMatcher->match($pathinfo);

        return $result;
    }

    /**
     * Gets the RouteCollection instance associated with this Router.
     *
     * @return \Symfony\Component\Routing\RouteCollection A RouteCollection instance
     */
    public function getRouteCollection(): RouteCollection
    {
        if (null === $this->routeCollection) {
            $this->routeCollection = new RouteCollection();
            $this->initRoutes();
        }

        return $this->routeCollection;
    }

    /**
     * Gets the request context.
     *
     * @api
     */
    public function getContext(): RequestContext
    {
        return $this->context;
    }

    /**
     * Sets the request context.
     *
     * @param RequestContext $context The context
     *
     * @api
     */
    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
    }

    private function initRoutes(): void
    {
        $redirects = $this->redirectRepository->findAll();

        /** @var Redirect $redirect */
        foreach ($redirects as $redirect) {
            // Check for wildcard routing and adjust as required
            if ($this->isWildcardRedirect($redirect)) {
                $route = $this->createWildcardRoute($redirect);
            } else {
                $route = $this->createRoute($redirect);
            }

            // Only add the route when the domain matches or the domain is empty
            if (!$redirect->getDomain()) {
                $this->routeCollection->add(
                    '_redirect_route_' . $redirect->getId(),
                    $route
                );
            }
        }
    }

    private function isWildcardRedirect(Redirect $redirect): bool
    {
        $origin = $redirect->getOrigin();
        $matchSegment = substr($origin, 0, -1);
        if (substr($origin, -2) === '/*') {
            return $this->isPathInfoWildcardMatch($matchSegment);
        }

        return false;
    }

    private function isPathInfoWildcardMatch($matchSegment): bool
    {
        $path = $this->context->getPathInfo();

        return strstr($path, $matchSegment);
    }

    private function createRoute(Redirect $redirect): Route
    {
        $needsUtf8 = false;
        foreach ([$redirect->getOrigin(), $redirect->getTarget()] as $item) {
            if (preg_match('/[\x80-\xFF]/', $item)) {
                $needsUtf8 = true;

                break;
            }
        }

        return new Route(
            $redirect->getOrigin(), [
                '_controller' => RedirectController::class.'::urlRedirectAction',
                'path' => $redirect->getTarget(),
                'permanent' => $redirect->isPermanent(),
            ], [], ['utf8' => $needsUtf8]);
    }

    /**
     * @return Route
     */
    private function createWildcardRoute(Redirect $redirect): Route
    {
        $origin = $redirect->getOrigin();
        $target = $redirect->getTarget();
        $url = $this->context->getPathInfo();
        $needsUtf8 = preg_match('/[\x80-\xFF]/', $redirect->getTarget());

        $origin = rtrim($origin, '/*');
        $target = rtrim($target, '/');
        $targetPath = str_replace($origin, $target, $url);

        $this->context->setPathInfo($targetPath);

        return new Route($url, [
            '_controller' => RedirectController::class.'::urlRedirectAction',
            'path' => $targetPath,
            'permanent' => $redirect->isPermanent(),
        ], [], ['utf8' => $needsUtf8]);
    }
}
