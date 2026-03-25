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

namespace Tappet\Bundle\Tests\Functional\Util\Kernel;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Tappet\Bundle\TappetBundle;

/**
 * Class TestKernel.
 *
 * Kernel that is solely used for functional testing of the bundle.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestKernel extends Kernel
{
    /**
     * @inheritDoc
     */
    public function getCacheDir(): string
    {
        return dirname(__DIR__, 4) . '/var/' . $this->environment . '/cache';
    }

    /**
     * @inheritDoc
     */
    public function getLogDir(): string
    {
        return dirname(__DIR__, 4) . '/var/' . $this->environment . '/logs';
    }

    /**
     * @inheritDoc
     */
    public function getProjectDir(): string
    {
        return __DIR__;
    }

    /**
     * @inheritDoc
     */
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TappetBundle(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
