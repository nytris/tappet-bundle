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
use Tappet\Api\RemoteState\DelegatingRemoteStateProvider;
use Tappet\Bundle\DependencyInjection\TappetExtension;

/**
 * Class RegisterRemoteStateProvidersPass.
 *
 * Wires all services tagged with "tappet.remote_state_provider" into the
 * DelegatingRemoteStateProvider. The tag is applied automatically via autoconfiguration
 * to any service whose class implements {@see RemoteStateProviderInterface} by the setup in {@see TappetExtension}.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RegisterRemoteStateProvidersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(DelegatingRemoteStateProvider::class)) {
            return;
        }

        $delegatingProviderDef = $container->getDefinition(DelegatingRemoteStateProvider::class);

        foreach ($container->findTaggedServiceIds(TappetExtension::REMOTE_STATE_PROVIDER_TAG) as $id => $tags) {
            $delegatingProviderDef->addMethodCall('registerProvider', [new Reference($id)]);
        }
    }
}
