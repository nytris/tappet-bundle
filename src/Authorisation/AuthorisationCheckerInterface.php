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

namespace Tappet\Bundle\Authorisation;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Interface AuthorisationCheckerInterface.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
interface AuthorisationCheckerInterface
{
    /**
     * Checks that the request carries valid authorisation credentials for the Tappet API.
     *
     * @throws AccessDeniedHttpException when authorisation fails.
     */
    public function checkAuthorisation(Request $request): void;
}
