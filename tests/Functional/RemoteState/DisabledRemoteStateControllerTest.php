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

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Tappet\Bundle\Controller\Api\RemoteStateController;
use Tappet\Bundle\Tests\Functional\AbstractWebTestCase;

/**
 * Class DisabledRemoteStateControllerTest.
 *
 * Verifies that {@see RemoteStateController} returns 404 when `tappet.enabled` is false.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DisabledRemoteStateControllerTest extends AbstractWebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient(['environment' => 'test_disabled']);
    }

    public function testFetchRemoteStateReturns404WhenDisabled(): void
    {
        $this->client->request(
            'GET',
            '/.well-known/tappet/remote-state/user-activation-token',
        );

        static::assertResponseStatusCodeSame(404);
    }
}
