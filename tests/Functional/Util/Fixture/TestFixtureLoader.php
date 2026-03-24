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

namespace Tappet\Bundle\Tests\Functional\Util\Fixture;

use Tappet\Api\Fixture\Loader\FixtureLoaderInterface;
use Tappet\Api\Fixture\Loader\LoaderPair;

/**
 * Class TestFixtureLoader.
 *
 * Registers load/unload behaviour for TestFixture in functional tests.
 *
 * @implements FixtureLoaderInterface<TestFixture, TestModel>
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestFixtureLoader implements FixtureLoaderInterface
{
    /**
     * @var list<array{fixture: TestFixture, model: TestModel}>
     */
    private array $unloadedPairs = [];

    /**
     * @inheritDoc
     */
    public function getLoaderPairs(): array
    {
        return [
            TestFixture::class => new LoaderPair(
                fn (TestFixture $fixture): TestModel => new TestModel($fixture->getDesiredName()),
                function (TestFixture $fixture, TestModel $model): void {
                    $this->unloadedPairs[] = ['fixture' => $fixture, 'model' => $model];
                },
            ),
        ];
    }

    /**
     * Returns all fixture/model pairs that have been unloaded via this loader.
     *
     * @return list<array{fixture: TestFixture, model: TestModel}>
     */
    public function getUnloadedPairs(): array
    {
        return $this->unloadedPairs;
    }
}
