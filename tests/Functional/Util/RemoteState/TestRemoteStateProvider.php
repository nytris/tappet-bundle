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

namespace Tappet\Bundle\Tests\Functional\Util\RemoteState;

use stdClass;
use Tappet\Api\RemoteState\RemoteStateProviderInterface;

/**
 * Class TestRemoteStateProvider.
 *
 * Registers remote state values for functional tests.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestRemoteStateProvider implements RemoteStateProviderInterface
{
    private stdClass $product;
    private string $userActivationToken = 'initial-token';

    /**
     * @inheritDoc
     */
    public function getProviders(): array
    {
        return [
            'product' => fn (): stdClass => $this->product,
            'user-activation-token' => fn (): string => $this->userActivationToken,
        ];
    }

    /**
     * Sets the product that will be returned for the "product" handle.
     */
    public function setProduct(stdClass $product): void
    {
        $this->product = $product;
    }

    /**
     * Sets the activation token that will be returned for the "user-activation-token" handle.
     */
    public function setUserActivationToken(string $userActivationToken): void
    {
        $this->userActivationToken = $userActivationToken;
    }
}
