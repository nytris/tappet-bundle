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
 * Class AuthorisationChecker.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
class AuthorisationChecker implements AuthorisationCheckerInterface
{
    public function __construct(
        private readonly ?string $apiKey,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function checkAuthorisation(Request $request): void
    {
        if ($this->apiKey === null) {
            return;
        }

        $authorisationHeader = $request->headers->get('Authorization', '');

        if ($authorisationHeader !== 'Bearer ' . $this->apiKey) {
            throw new AccessDeniedHttpException();
        }
    }
}
