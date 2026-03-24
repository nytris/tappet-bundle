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
use Tappet\Bundle\Tests\Functional\Util\Fixture\AnotherTestModel;
use Tappet\Bundle\Tests\Functional\Util\Fixture\TestFixture;
use Tappet\Bundle\Tests\Functional\Util\Fixture\TestFixtureLoader;
use Tappet\Bundle\Tests\Functional\Util\Fixture\TestModel;

/**
 * Class FixtureControllerTest.
 *
 * End-to-end functional tests for FixtureController, exercising the complete
 * path from TappetBundle's auto-registered routes through to Tappet's
 * DelegatingFixtureLoader.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class FixtureControllerTest extends AbstractWebTestCase
{
    private KernelBrowser $client;
    private TestFixtureLoader $testFixtureLoader;

    public function setUp(): void
    {
        $this->client = static::createClient(['environment' => 'test']);

        /*
         * TestFixtureLoader is declared as a service in config_test.yml with `autoconfigure: true`.
         * TappetExtension::load() calls
         * `registerForAutoconfiguration(FixtureLoaderInterface::class)
         *     ->addTag('tappet.fixture_loader')`,
         * so the tag is applied automatically.
         *
         * RegisterFixtureLoadersPass then wires it into DelegatingFixtureLoader at compile time.
         * We retrieve the same singleton here to inspect its state after HTTP requests.
         */
        $this->testFixtureLoader = static::getContainer()->get(TestFixtureLoader::class);
    }

    public function testLoadFixtureCreatesAndReturnsSerializedModel(): void
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

        static::assertResponseIsSuccessful();

        $body = json_decode((string) $this->client->getResponse()->getContent(), true);
        /** @var TestModel $model */
        $model = unserialize($body['serialisation']);

        static::assertInstanceOf(TestModel::class, $model);
        static::assertSame('my-widget', $model->name);
    }

    public function testLoadFixtureUsesFixtureStateToProduceModel(): void
    {
        $fixture = new TestFixture('another-widget');
        $fixtureClass = str_replace('\\', '--', TestFixture::class);

        $this->client->request(
            'POST',
            '/.well-known/tappet/fixture/' . $fixtureClass,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['serialisation' => serialize($fixture)]),
        );

        $body = json_decode((string) $this->client->getResponse()->getContent(), true);
        /** @var TestModel $model */
        $model = unserialize($body['serialisation']);

        static::assertSame('another-widget', $model->name);
    }

    public function testLoadFixtureReturns422ForUnknownFixtureClass(): void
    {
        $this->client->request(
            'POST',
            '/.well-known/tappet/fixture/No--Such--Class',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['serialisation' => null]),
        );

        static::assertResponseStatusCodeSame(422);
    }

    public function testPurgeFixturesUnloadsEachFixtureViaLoader(): void
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
            ]),
        );

        static::assertResponseStatusCodeSame(204);

        $unloaded = $this->testFixtureLoader->getUnloadedPairs();
        static::assertCount(1, $unloaded);
        static::assertSame('to-delete', $unloaded[0]['fixture']->getDesiredName());
        static::assertSame('to-delete', $unloaded[0]['model']->name);
    }

    public function testPurgeFixturesUnloadsMultipleFixtures(): void
    {
        $fixtureA = new TestFixture('alpha');
        $modelA = new TestModel('alpha');
        $fixtureB = new TestFixture('beta');
        $modelB = new TestModel('beta');

        $this->client->request(
            'DELETE',
            '/.well-known/tappet/fixtures',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                ['fixture' => serialize($fixtureA), 'model' => serialize($modelA)],
                ['fixture' => serialize($fixtureB), 'model' => serialize($modelB)],
            ]),
        );

        static::assertResponseStatusCodeSame(204);

        $unloaded = $this->testFixtureLoader->getUnloadedPairs();
        static::assertCount(2, $unloaded);
        static::assertSame('alpha', $unloaded[0]['fixture']->getDesiredName());
        static::assertSame('alpha', $unloaded[0]['model']->name);
        static::assertSame('beta', $unloaded[1]['fixture']->getDesiredName());
        static::assertSame('beta', $unloaded[1]['model']->name);
    }

    public function testPurgeFixturesReturns422ForInvalidFixtureSerialisation(): void
    {
        $this->client->request(
            'DELETE',
            '/.well-known/tappet/fixtures',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                ['fixture' => 'not-valid-serialisation', 'model' => serialize(new TestModel('x'))],
            ]),
        );

        static::assertResponseStatusCodeSame(422);
    }

    public function testPurgeFixturesReturns422ForInvalidModelSerialisation(): void
    {
        $fixture = new TestFixture('x');

        $this->client->request(
            'DELETE',
            '/.well-known/tappet/fixtures',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                ['fixture' => serialize($fixture), 'model' => 'not-valid-serialisation'],
            ]),
        );

        static::assertResponseStatusCodeSame(422);
    }

    public function testPurgeFixturesReturns422WhenModelClassDoesNotMatchFixture(): void
    {
        $fixture = new TestFixture('x');
        $model = new AnotherTestModel('x');

        $this->client->request(
            'DELETE',
            '/.well-known/tappet/fixtures',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                ['fixture' => serialize($fixture), 'model' => serialize($model)],
            ]),
        );

        static::assertResponseStatusCodeSame(422);
    }
}
