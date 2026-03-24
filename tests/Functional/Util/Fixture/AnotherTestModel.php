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

use Tappet\Core\Fixture\ModelInterface;

/**
 * Class AnotherTestModel.
 *
 * A second ModelInterface implementation used to test model class mismatch in functional tests.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AnotherTestModel implements ModelInterface
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}
