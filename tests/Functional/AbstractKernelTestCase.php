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

namespace Tappet\Bundle\Tests\Functional;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class AbstractKernelTestCase.
 *
 * Base class for all kernel test cases.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
abstract class AbstractKernelTestCase extends KernelTestCase
{
    use MockeryPHPUnitIntegration;
}
