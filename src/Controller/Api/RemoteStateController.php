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

namespace Tappet\Bundle\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tappet\Api\RemoteState\DelegatingRemoteStateProviderInterface;
use Tappet\Bundle\Authorisation\AuthorisationCheckerInterface;
use Tappet\Runner\Assertion\RemoteStateAssertionInterface;

/**
 * Class RemoteStateController.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
#[AsController]
class RemoteStateController
{
    public function __construct(
        private readonly DelegatingRemoteStateProviderInterface $delegatingRemoteStateProvider,
        private readonly AuthorisationCheckerInterface $authorisationChecker,
        private readonly bool $enabled,
    ) {
    }

    /**
     * Fetches a remote state value by its handle, for assertion by a GUI test
     * via a {@see RemoteStateAssertionInterface} of some state fetched from the backend
     * via a Tappet API endpoint that is either:
     *
     *    (a) Not present on the page itself, but must instead be fetched from the backend -
     *        for example, a value deliberately never surfaced in the UI for security reasons.
     *    (b) Presented on a page, but that page is not directly relevant to the scenario under test,
     *        and so it would be needlessly inefficient to load that page just to check this value.
     *        Additionally, a bug on that display page could then lead to breaking tests
     *        that are not directly related to it, as they are only using it to surface a value from the backend.
     */
    public function fetchAction(Request $request, string $handle): JsonResponse
    {
        if (!$this->enabled) {
            throw new NotFoundHttpException();
        }

        $this->authorisationChecker->checkAuthorisation($request);

        $value = $this->delegatingRemoteStateProvider->fetchRemoteState($handle);

        return new JsonResponse(['serialisation' => serialize($value)]);
    }
}
