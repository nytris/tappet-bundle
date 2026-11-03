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

namespace Tappet\Bundle\Tests\Functional\RemoteState;

use stdClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Tappet\Api\RemoteState\DelegatingRemoteStateProvider;
use Tappet\Bundle\Controller\Api\RemoteStateController;
use Tappet\Bundle\Tests\Functional\AbstractWebTestCase;
use Tappet\Bundle\Tests\Functional\Util\RemoteState\TestRemoteStateProvider;

/**
 * Class RemoteStateControllerTest.
 *
 * End-to-end functional tests for {@see RemoteStateController}, exercising the complete
 * path from TappetBundle's auto-registered routes through to Tappet's
 * {@see DelegatingRemoteStateProvider}.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class RemoteStateControllerTest extends AbstractWebTestCase
{
    private KernelBrowser $client;
    private TestRemoteStateProvider $testRemoteStateProvider;

    public function setUp(): void
    {
        $this->client = static::createClient(['environment' => 'test']);

        /*
         * TestRemoteStateProvider is declared as a service in config_test.yml with `autoconfigure: true`.
         * TappetExtension::load() calls
         * `registerForAutoconfiguration(RemoteStateProviderInterface::class)
         *     ->addTag('tappet.remote_state_provider')`,
         * so the tag is applied automatically.
         *
         * RegisterRemoteStateProvidersPass then wires it into DelegatingRemoteStateProvider
         * at compile time. We retrieve the same singleton here to configure its state
         * before making HTTP requests.
         */
        $this->testRemoteStateProvider = static::getContainer()->get(TestRemoteStateProvider::class);
    }

    public function testFetchRemoteStateReturnsSerializedValueWhenScalar(): void
    {
        $this->testRemoteStateProvider->setUserActivationToken('abc123');

        $this->client->request(
            'GET',
            '/.well-known/tappet/remote-state/user-activation-token',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer test-api-key'],
        );

        static::assertResponseIsSuccessful();

        $body = json_decode((string) $this->client->getResponse()->getContent(), true);
        $value = unserialize($body['serialisation']);

        static::assertSame('abc123', $value);
    }

    public function testFetchRemoteStateReturnsSerializedValueWhenNonScalar(): void
    {
        $product = new stdClass();
        $product->line = 'cassettes';
        $this->testRemoteStateProvider->setProduct($product);

        $this->client->request(
            'GET',
            '/.well-known/tappet/remote-state/product',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer test-api-key'],
        );

        static::assertResponseIsSuccessful();

        $body = json_decode((string) $this->client->getResponse()->getContent(), true);
        $value = unserialize($body['serialisation']);

        static::assertEquals($product, $value);
    }

    public function testFetchRemoteStateReturns403WhenAuthorizationHeaderIsMissing(): void
    {
        $this->client->request('GET', '/.well-known/tappet/remote-state/user-activation-token');

        static::assertResponseStatusCodeSame(403);
    }

    public function testFetchRemoteStateReturns403WhenAuthorizationHeaderIsIncorrect(): void
    {
        $this->client->request(
            'GET',
            '/.well-known/tappet/remote-state/user-activation-token',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer incorrect-key'],
        );

        static::assertResponseStatusCodeSame(403);
    }

    public function testFetchRemoteStateReturns500ForUnknownHandle(): void
    {
        $this->client->request(
            'GET',
            '/.well-known/tappet/remote-state/unknown-handle',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer test-api-key'],
        );

        static::assertResponseStatusCodeSame(500);
    }
}
