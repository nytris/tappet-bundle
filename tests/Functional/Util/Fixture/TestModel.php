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

use Tappet\Common\Fixture\ModelInterface;

/**
 * Class TestModel.
 *
 * A minimal ModelInterface implementation used in functional tests.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class TestModel implements ModelInterface
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
