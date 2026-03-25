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

namespace Tappet\Bundle\Tests\Functional\Fixture;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Tappet\Bundle\Tests\Functional\AbstractWebTestCase;
use Tappet\Bundle\Tests\Functional\Util\Fixture\TestFixture;
use Tappet\Bundle\Tests\Functional\Util\Fixture\TestModel;

/**
 * Class DisabledFixtureControllerTest.
 *
 * Verifies that FixtureController actions return 404 when `tappet.enabled` is false.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class DisabledFixtureControllerTest extends AbstractWebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient(['environment' => 'test_disabled']);
    }

    public function testLoadFixtureReturns404WhenDisabled(): void
    {
        $fixture = new TestFixture('my-widget');
        $fixtureClass = str_replace('\\', '--', TestFixture::class);

        $this->client->request(
            'POST',
            '/.well-known/tappet/fixture/' . $fixtureClass,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['serialisation' => serialize($fixture)]),
        );

        static::assertResponseStatusCodeSame(404);
    }

    public function testLoadMultipleFixturesReturns404WhenDisabled(): void
    {
        $fixture = new TestFixture('my-widget');

        $this->client->request(
            'POST',
            '/.well-known/tappet/fixtures',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['serialisation' => serialize(['handle' => $fixture])], JSON_THROW_ON_ERROR),
        );

        static::assertResponseStatusCodeSame(404);
    }

    public function testPurgeFixturesReturns404WhenDisabled(): void
    {
        $fixture = new TestFixture('to-delete');
        $model = new TestModel('to-delete');

        $this->client->request(
            'DELETE',
            '/.well-known/tappet/fixtures',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                ['fixture' => serialize($fixture), 'model' => serialize($model)],
            ], JSON_THROW_ON_ERROR),
        );

        static::assertResponseStatusCodeSame(404);
    }
}
