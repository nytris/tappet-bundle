# Tappet Bundle

[![Build Status](https://github.com/nytris/tappet-bundle/workflows/CI/badge.svg)](https://github.com/nytris/tappet-bundle/actions?query=workflow%3ACI)

> **[EXPERIMENTAL]** - API is unstable and subject to change.

Integrates [Tappet](https://github.com/nytris/tappet) into a [Symfony](https://symfony.com) application.
Exposes the fixture API endpoints that Tappet uses to load and tear down test data, so you do not need
to implement them by hand.

## Installation

```bash
composer require tappet/bundle
```

## Setup

### 1. Register the bundle

Add the bundle to `config/bundles.php`, enabled only for the environment in which you run Tappet
(e.g. a dedicated `cypress` environment, or `test`):

```php
<?php

return [
    // ...
    Tappet\Bundle\TappetBundle::class => ['cypress' => true],
];
```

### 2. Configure the bundle

Create `config/packages/cypress/tappet.yaml` (adjust the directory name to match your environment):

```yaml
tappet:
    enabled: true
    api_key: '%env(TAPPET_API_KEY)%'
```

Set the same key in your `.env.cypress` (or pass it via the environment):

```
TAPPET_API_KEY=your-api-key
```

Use the same value for `tappetApiKey` in `tappet.config.php` and in your adapter config.
See the [Tappet core README](https://github.com/nytris/tappet) for details.

### 3. Write fixture loaders

For each fixture type, create a service that implements `FixtureLoaderInterface`. The bundle
auto-discovers these via Symfony's autoconfiguration; no manual tagging needed.

```php
<?php

declare(strict_types=1);

namespace App\Tests\Tappet\Fixture\Loader;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Tappet\Fixture\UserFixture;
use App\Tests\Tappet\Fixture\UserModel;
use Tappet\Api\Fixture\Loader\FixtureLoaderInterface;
use Tappet\Api\Fixture\Loader\LoaderPair;

/**
 * @implements FixtureLoaderInterface<UserFixture, UserModel>
 */
class UserFixtureLoader implements FixtureLoaderInterface
{
    public function __construct(private readonly UserRepository $userRepository) {}

    public function getLoaderPairs(): array
    {
        return [
            UserFixture::class => new LoaderPair(
                loader: function (UserFixture $fixture): UserModel {
                    $user = new User(
                        firstName: $fixture->getFirstName(),
                        lastName: $fixture->getLastName(),
                        email: $fixture->getEmail(),
                    );
                    $this->userRepository->save($user, flush: true);

                    return new UserModel($user->getId());
                },
                unloader: function (UserFixture $fixture, UserModel $model): void {
                    $user = $this->userRepository->find($model->getId());

                    if ($user !== null) {
                        $this->userRepository->remove($user, flush: true);
                    }
                },
            ),
        ];
    }
}
```

The `loader` callable receives the fixture, creates the corresponding record, and returns a model.
The `unloader` callable receives both the fixture and the model and deletes the record.

Tappet calls the `unloader` automatically after each scenario completes, whether it passed or failed.

## Fixture API endpoints

The bundle registers these routes automatically:

| Method   | Path                                  | Purpose                                       |
|----------|---------------------------------------|-----------------------------------------------|
| `POST`   | `/.well-known/tappet/fixture/{class}` | Create a single fixture                       |
| `POST`   | `/.well-known/tappet/fixtures`        | Create multiple fixtures (in bulk)            |
| `DELETE` | `/.well-known/tappet/fixtures`        | Delete all fixtures created in this scenario  |

All requests are validated against the `Authorization: Bearer <key>` header using the configured
`api_key`. The `api_key` setting is required and may not be empty: requests that do not present the
correct key are rejected with a 403 response.

## Fixture and model classes

Write fixture and model classes as described in the
[Tappet core README](https://github.com/nytris/tappet#fixtures).
It is suggested to keep them inside your test directory:

```
tests/
└── Tappet/
    └── Fixture/
        ├── Loader/
        │   └── UserFixtureLoader.php
        ├── UserFixture.php
        └── UserModel.php
```

## Licence

[MIT](MIT-LICENSE.txt)
