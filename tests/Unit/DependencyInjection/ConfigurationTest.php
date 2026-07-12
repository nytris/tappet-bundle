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

namespace Tappet\Bundle\Tests\Unit\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;
use Tappet\Bundle\DependencyInjection\Configuration;
use Tappet\Bundle\Tests\AbstractTestCase;

/**
 * Class ConfigurationTest.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class ConfigurationTest extends AbstractTestCase
{
    private Configuration $configuration;
    private Processor $processor;

    public function setUp(): void
    {
        $this->configuration = new Configuration();
        $this->processor = new Processor();
    }

    public function testProcessesValidConfiguration(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            ['enabled' => true, 'api_key' => 'my-api-key'],
        ]);

        static::assertTrue($config['enabled']);
        static::assertSame('my-api-key', $config['api_key']);
    }

    public function testEnabledDefaultsToFalse(): void
    {
        $config = $this->processor->processConfiguration($this->configuration, [
            ['api_key' => 'my-api-key'],
        ]);

        static::assertFalse($config['enabled']);
    }

    public function testApiKeyIsRequiredWhenOmitted(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "api_key" under "tappet" must be configured.');

        $this->processor->processConfiguration($this->configuration, [
            ['enabled' => true],
        ]);
    }

    public function testApiKeyIsRequiredEvenWhenBundleIsDisabled(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child config "api_key" under "tappet" must be configured.');

        $this->processor->processConfiguration($this->configuration, [
            ['enabled' => false],
        ]);
    }

    public function testApiKeyCannotBeNull(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The path "tappet.api_key" cannot contain an empty value, but got null.');

        $this->processor->processConfiguration($this->configuration, [
            ['enabled' => true, 'api_key' => null],
        ]);
    }

    public function testApiKeyCannotBeEmpty(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The path "tappet.api_key" cannot contain an empty value, but got "".');

        $this->processor->processConfiguration($this->configuration, [
            ['enabled' => true, 'api_key' => ''],
        ]);
    }
}
