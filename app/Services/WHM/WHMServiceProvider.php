<?php

namespace App\Services\WHM;

use Illuminate\Support\ServiceProvider;

class WHMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WhmServerDetails::class, function () {
            return new WhmServerDetails;
        });

        $this->app->singleton(WhmEmailDiskUsage::class, function () {
            return new WhmEmailDiskUsage;
        });

        $this->app->singleton(WhmAccountDetails::class, function () {
            return new WhmAccountDetails;
        });
    }
}
