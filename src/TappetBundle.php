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

namespace Tappet\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tappet\Bundle\DependencyInjection\Compiler\RegisterBundleRoutesPass;
use Tappet\Bundle\DependencyInjection\Compiler\RegisterFixtureLoadersPass;

/**
 * Class TappetBundle.
 *
 * Configures Tappet for a Symfony application.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TappetBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterBundleRoutesPass());
        $container->addCompilerPass(new RegisterFixtureLoadersPass());
    }
}
