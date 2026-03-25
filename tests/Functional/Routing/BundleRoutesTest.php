<?php

/*
 * Tappet Bundle
 * Copyright (c) Dan Phillimore (asmblah)
 * https://github.com/nytris/tappet-bundle/
 *
 * Released under the MIT license.
 * https://github.com/nytris/tappet-bundle/raw/main/MIT-LICENSE.txt
 */

declare(strict_types=1);

namespace Tappet\Bundle\Tests\Functional\Routing;

use Symfony\Component\Routing\RouterInterface;
use Tappet\Bundle\Tests\Functional\AbstractKernelTestCase;

/**
 * Class BundleRoutesTest.
 *
 * Verifies that the bundle's routes are automatically registered in the consuming
 * application's router without requiring any explicit import in the app's routing
 * configuration. This exercises the `RegisterBundleRoutesPass` compiler pass and
 * the `BundleRouteLoader` decoration of `routing.loader` end-to-end.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BundleRoutesTest extends AbstractKernelTestCase
{
    private RouterInterface $router;

    public function setUp(): void
    {
        static::bootKernel(['environment' => 'test']);

        /** @var RouterInterface $router */
        $router = static::$kernel->getContainer()->get('router');
        $this->router = $router;
    }

    public function testLoadFixturesRouteIsAutomaticallyRegistered(): void
    {
        $route = $this->router->getRouteCollection()->get('tappet_api_load_fixtures');

        static::assertNotNull(
            $route,
            'Expected route "tappet_api_load_fixtures" to be registered automatically by the bundle'
        );
    }

    public function testLoadFixturesRouteHasCorrectPath(): void
    {
        $route = $this->router->getRouteCollection()->get('tappet_api_load_fixtures');

        static::assertSame('/.well-known/tappet/fixture/{fixtureClass}', $route->getPath());
    }

    public function testLoadFixturesRouteOnlyAcceptsPostRequests(): void
    {
        $route = $this->router->getRouteCollection()->get('tappet_api_load_fixtures');

        static::assertSame(['POST'], $route->getMethods());
    }

    public function testDeleteFixturesRouteIsAutomaticallyRegistered(): void
    {
        $route = $this->router->getRouteCollection()->get('tappet_api_purge_fixtures');

        static::assertNotNull(
            $route,
            'Expected route "tappet_api_purge_fixtures" to be registered automatically by the bundle'
        );
    }

    public function testDeleteFixturesRouteHasCorrectPath(): void
    {
        $route = $this->router->getRouteCollection()->get('tappet_api_purge_fixtures');

        static::assertSame('/.well-known/tappet/fixtures', $route->getPath());
    }

    public function testDeleteFixturesRouteOnlyAcceptsDeleteRequests(): void
    {
        $route = $this->router->getRouteCollection()->get('tappet_api_purge_fixtures');

        static::assertSame(['DELETE'], $route->getMethods());
    }

    public function testLoadFixturesRouteIsMatchable(): void
    {
        $this->router->getContext()->setMethod('POST');

        $params = $this->router->match('/.well-known/tappet/fixture/My--Fixture');

        static::assertSame('tappet_api_load_fixtures', $params['_route']);
        static::assertSame('My--Fixture', $params['fixtureClass']);
    }

    public function testDeleteFixturesRouteIsMatchable(): void
    {
        $this->router->getContext()->setMethod('DELETE');

        $params = $this->router->match('/.well-known/tappet/fixtures');

        static::assertSame('tappet_api_purge_fixtures', $params['_route']);
    }
}
