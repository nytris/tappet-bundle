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

namespace Tappet\Bundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class BundleRouteLoader.
 *
 * Decorates Symfony's routing.loader (DelegatingLoader) so that the bundle's
 * own routes are automatically merged into the application's route collection
 * without requiring an explicit import in the consuming app's routes config.
 *
 * The decoration intercepts the single top-level load() call made by the Router
 * service. Nested imports within the app's route files are resolved directly
 * through sub-loaders (via LoaderResolver), so this loader is only invoked once.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class BundleRouteLoader implements LoaderInterface
{
    public function __construct(
        private readonly LoaderInterface $inner,
        private readonly string $bundleRoutesConfig,
    ) {
    }

    /**
     * @inheritdoc
     */
    public function getResolver(): LoaderResolverInterface
    {
        return $this->inner->getResolver();
    }

    /**
     * @inheritdoc
     */
    public function load(mixed $resource, ?string $type = null): RouteCollection
    {
        /** @var RouteCollection $collection */
        $collection = $this->inner->load($resource, $type);

        /** @var RouteCollection $bundleRoutes */
        $bundleRoutes = $this->inner->load($this->bundleRoutesConfig);
        $collection->addCollection($bundleRoutes);

        return $collection;
    }

    /**
     * @inheritdoc
     */
    public function setResolver(LoaderResolverInterface $resolver): void
    {
        $this->inner->setResolver($resolver);
    }

    /**
     * @inheritdoc
     */
    public function supports(mixed $resource, ?string $type = null): bool
    {
        return $this->inner->supports($resource, $type);
    }
}
