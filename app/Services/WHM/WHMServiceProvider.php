<?php

namespace App\Services\WHM;

use Illuminate\Support\ServiceProvider;

class WHMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WhmApi::class, function () {
            return new WhmApi();
        });
    }
}
