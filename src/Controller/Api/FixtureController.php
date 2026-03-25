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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Tappet\Api\Fixture\Loader\DelegatingFixtureLoaderInterface;
use Tappet\Core\Fixture\FixtureInterface;
use Tappet\Core\Fixture\ModelInterface;

/**
 * Class FixtureController.
 *
 * @author Dan Phillimore <dan@ovms.co>
 */
#[AsController]
class FixtureController
{
    public function __construct(
        private readonly DelegatingFixtureLoaderInterface $delegatingFixtureLoader,
    ) {
    }

    /**
     * Deletes one or more loaded fixtures in bulk as identified by their models.
     *
     * Expects a JSON body of the form:
     * [{"fixture": "<serialised FixtureInterface>", "model": "<serialised ModelInterface>"}, ...]
     */
    public function purgeAction(Request $request): Response
    {
        $body = (array) json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);

        foreach ($body as $item) {
            $item = (array) $item;

            $fixture = @unserialize($item['fixture'] ?? '');
            $model = @unserialize($item['model'] ?? '');

            if (!$fixture instanceof FixtureInterface) {
                throw new UnprocessableEntityHttpException(sprintf(
                    'Fixture failed to deserialise as an instance of "%s"',
                    FixtureInterface::class
                ));
            }

            if (!$model instanceof ModelInterface) {
                throw new UnprocessableEntityHttpException(sprintf(
                    'Model failed to deserialise as an instance of "%s"',
                    ModelInterface::class
                ));
            }

            $modelClass = $fixture::getModelClass();

            if (!($model instanceof $modelClass)) {
                throw new UnprocessableEntityHttpException(sprintf(
                    'Model is not an instance of the fixture\'s model class "%s"',
                    $modelClass
                ));
            }

            $this->delegatingFixtureLoader->unloadFixture($fixture, $model);
        }

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    /**
     * Loads a fixture.
     *
     * Expects a JSON body of the form:
     * {"serialisation": "<serialised FixtureInterface>"}
     */
    public function loadAction(Request $request, string $fixtureClass): JsonResponse
    {
        $fixtureClass = str_replace('--', '\\', $fixtureClass);

        if (!is_subclass_of($fixtureClass, FixtureInterface::class)) {
            throw new UnprocessableEntityHttpException(sprintf(
                'Invalid fixture class "%s"',
                $fixtureClass
            ));
        }

        $body = (array) json_decode($request->getContent(), true, flags: JSON_THROW_ON_ERROR);
        $fixtureSerialisation = $body['serialisation'] ?? null;

        $fixture = $fixtureSerialisation !== null ?
            unserialize($fixtureSerialisation) :
            null;

        if (!$fixture instanceof $fixtureClass) {
            throw new UnprocessableEntityHttpException(sprintf(
                'Fixture failed to deserialise as an instance of "%s"',
                $fixtureClass
            ));
        }

        $fixtureModel = $this->delegatingFixtureLoader->loadFixture($fixture);

        return new JsonResponse(['serialisation' => serialize($fixtureModel)]);
    }
}
