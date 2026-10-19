<?php

namespace Hgabka\SettingsBundle\Tests\Router;

use Doctrine\ORM\EntityRepository;
use Hgabka\SettingsBundle\Entity\Redirect;
use Hgabka\SettingsBundle\Router\RedirectRouter;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Controller\RedirectController;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;

class RedirectRouterTest extends TestCase
{
    private function redirect(int $id, string $origin, string $target, bool $permanent = false, ?string $domain = null): Redirect
    {
        $redirect = (new Redirect())->setOrigin($origin)->setTarget($target)->setPermanent($permanent)->setDomain($domain);
        (new \ReflectionProperty(Redirect::class, 'id'))->setValue($redirect, $id);

        return $redirect;
    }

    private function router(array $redirects, string $pathInfo = '/'): RedirectRouter
    {
        $repository = $this->createStub(EntityRepository::class);
        $repository->method('findAll')->willReturn($redirects);

        $router = new RedirectRouter($repository);
        $router->setContext((new RequestContext())->setPathInfo($pathInfo));

        return $router;
    }

    public function testMatchesExactOrigin(): void
    {
        $router = $this->router([$this->redirect(1, '/old', '/new', true)]);

        $match = $router->match('/old');

        $this->assertSame(RedirectController::class . '::urlRedirectAction', $match['_controller']);
        $this->assertSame('/new', $match['path']);
        $this->assertTrue($match['permanent']);
        $this->assertSame('_redirect_route_1', $match['_route']);
    }

    public function testNonPermanentRedirect(): void
    {
        $match = $this->router([$this->redirect(1, '/old', '/new')])->match('/old');

        $this->assertFalse($match['permanent']);
    }

    public function testUnknownPathIsNotFound(): void
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->router([$this->redirect(1, '/old', '/new')])->match('/other');
    }

    public function testEveryRedirectGetsItsOwnRoute(): void
    {
        $router = $this->router([$this->redirect(1, '/a', '/x'), $this->redirect(2, '/b', '/y')]);

        $this->assertCount(2, $router->getRouteCollection());
        $this->assertSame('/y', $router->match('/b')['path']);
    }

    public function testRedirectsWithDomainAreIgnored(): void
    {
        $router = $this->router([$this->redirect(1, '/old', '/new', false, 'example.com')]);

        $this->assertCount(0, $router->getRouteCollection());
    }

    public function testUtf8OriginIsSupported(): void
    {
        $router = $this->router([$this->redirect(1, '/régi', '/új')]);

        $this->assertSame('/új', $router->match('/régi')['path']);
        $this->assertTrue($router->getRouteCollection()->get('_redirect_route_1')->getOption('utf8'));
    }

    public function testWildcardRedirectRewritesThePrefix(): void
    {
        $router = $this->router([$this->redirect(1, '/blog/*', '/news')], '/blog/post-1');

        $match = $router->match('/blog/post-1');

        $this->assertSame('/news/post-1', $match['path']);
    }

    public function testWildcardOriginThatDoesNotMatchTheCurrentPathIsTreatedAsPlainRoute(): void
    {
        $router = $this->router([$this->redirect(1, '/blog/*', '/news')], '/shop');

        $this->expectException(ResourceNotFoundException::class);
        $router->match('/shop');
    }

    public function testUrlsCannotBeGenerated(): void
    {
        $this->expectException(RouteNotFoundException::class);
        $this->router([])->generate('anything');
    }

    public function testContextCanBeReplaced(): void
    {
        $router = $this->router([]);
        $context = new RequestContext('', 'GET', 'example.com');

        $router->setContext($context);

        $this->assertSame($context, $router->getContext());
    }
}
