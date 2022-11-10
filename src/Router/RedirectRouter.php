<?php

namespace Hgabka\SettingsBundle\Router;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityRepository;
use Hgabka\SettingsBundle\Entity\Redirect;
use Hgabka\SettingsBundle\Routing\Matcher\RedirectMatcher;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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
        $urlMatcher = new RedirectMatcher($this->getRouteCollection(), $this->getContext());
        $result = $urlMatcher->match($pathinfo);

        return $result;
    }

    /**
     * Gets the RouteCollection instance associated with this Router.
     *
     * @return \Symfony\Component\Routing\RouteCollection A RouteCollection instance
     */
    public function getRouteCollection()
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
    public function setContext(RequestContext $context)
    {
        $this->context = $context;
    }

    private function initRoutes()
    {
        $redirects = $this->redirectRepository->findAll();

        /** @var Redirect $redirect */
        foreach ($redirects as $redirect) {
            // Only add the route when the domain matches or the domain is empty
            if (!$redirect->getDomain()) {
                $this->routeCollection->add(
                    '_redirect_route_' . $redirect->getId(),
                    new Route($redirect->getOrigin(), [
                        '_controller' => RedirectController::class . '::urlRedirectAction',
                        'path' => $redirect->getTarget(),
                        'permanent' => $redirect->isPermanent(),
                    ])
                );
            }
        }
    }
}
