<?php

namespace Amp\Correlate\Laravel;

use Illuminate\Support\ServiceProvider;

class LumenCorrelateServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->middleware([
            LaravelCorrelateMiddleware::class,
        ]);
    }
}

