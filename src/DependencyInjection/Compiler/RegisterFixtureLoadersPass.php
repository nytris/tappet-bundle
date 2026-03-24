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
use Symfony\Component\DependencyInjection\Reference;
use Tappet\Api\Fixture\Loader\DelegatingFixtureLoader;

/**
 * Class RegisterFixtureLoadersPass.
 *
 * Wires all services tagged with "tappet.fixture_loader" into the
 * DelegatingFixtureLoader. The tag is applied automatically via autoconfiguration
 * to any service whose class implements FixtureLoaderInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RegisterFixtureLoadersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(DelegatingFixtureLoader::class)) {
            return;
        }

        $delegatingLoaderDef = $container->getDefinition(DelegatingFixtureLoader::class);

        foreach ($container->findTaggedServiceIds('tappet.fixture_loader') as $id => $tags) {
            $delegatingLoaderDef->addMethodCall('registerLoader', [new Reference($id)]);
        }
    }
}
