# correlate-php-laravel

---

## Overview

It's very difficult to track a request across the system when we are working with microservices. We came out with a solution for that. We generate a unique version 4 uuid for every request and every service passes this id via request headers to other services. We call this **correlation ID**.

## Installation

- Install via composer Satis Antel

```sh
composer require amp/correlate-php-laravel
```

## Setup for Laravel 5

Add the `Amp\Correlate\Laravel\LaravelCorrelateMiddleware` middleware to the $middleware property of your app/Http/Kernel.php class.

## Setup for Lumen 5

Add service provider to bootstrap/app.php in your Lumen project.

```php
// bootstrap/app.php

$app->register(\Amp\Correlate\Laravel\LaravelCorrelateServiceProvider::class);

```

## Usage

This middleware automatically adds correlation id (coming from request header) to every log message.
There are some macros added to the request object if you want to work with correlation id.

Using macros via request object:

```php
if ($request->hasCorrelationId()) {
  $cid = $request->getCorrelationId();
}
// or if you can change the ID
$request->setCorrelationId(\ProEmergotech\Correlate\Correlate::id());
```

## Contributing

See `CONTRIBUTING.md` file.

## Credits

This package was developed by [Soma Szélpál](https://github.com/shakahl/) at [Pro Emergotech Ltd.](https://github.com/proemergotech/).

Additional author is [Miklós Boros](https://github.com/cherubmiki) at [Pro Emergotech Ltd.](https://github.com/proemergotech/).

## License

This project is released under the [MIT License](http://www.opensource.org/licenses/MIT).
