# Tappet Bundle

[![Build Status](https://github.com/nytris/tappet-bundle/workflows/CI/badge.svg)](https://github.com/nytris/tappet-bundle/actions?query=workflow%3ACI)

Integrates [Tappet][Tappet] into a [Symfony][Symfony] application.

## Usage
Install this package with Composer:

```shell
$ composer install tappet/bundle
```

Then, add the bundle to your application's kernel:

`config/bundles.php`:
```php
<?php

return [
    // ...
    Tappet\Bundle\TappetBundle::class => ['cypress' => true],
];
```

[Symfony]: https://symfony.com
[Tappet]: https://github.com/nytris/tappet
