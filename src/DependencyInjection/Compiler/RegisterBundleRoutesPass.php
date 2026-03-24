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

namespace Tappet\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Tappet\Bundle\Routing\BundleRouteLoader;

/**
 * Class RegisterBundleRoutesPass.
 *
 * Decorates the `routing.loader` service with BundleRouteLoader so that the
 * bundle's routes are automatically registered in the consuming application
 * without requiring an explicit import in the app's routing configuration.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RegisterBundleRoutesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('routing.loader')) {
            // Router is not configured in this application; nothing to do.
            return;
        }

        $definition = new Definition(BundleRouteLoader::class);
        $definition->setDecoratedService('routing.loader');
        $definition->setArguments([
            new Reference(BundleRouteLoader::class . '.inner'),
            __DIR__ . '/../../Resources/routing/routes.yml',
        ]);

        $container->setDefinition(BundleRouteLoader::class, $definition);
    }
}
