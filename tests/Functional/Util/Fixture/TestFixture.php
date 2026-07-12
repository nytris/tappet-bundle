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

use Tappet\Common\Fixture\AbstractFixture;

/**
 * Class TestFixture.
 *
 * A minimal FixtureInterface implementation used in functional tests.
 *
 * @extends AbstractFixture<TestModel>
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestFixture extends AbstractFixture
{
    public function __construct(
        private readonly string $desiredName,
    ) {
    }

    public function getDesiredName(): string
    {
        return $this->desiredName;
    }

    /**
     * @inheritDoc
     */
    public static function getModelClass(): string
    {
        return TestModel::class;
    }
}
