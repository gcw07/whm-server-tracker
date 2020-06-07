<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeComponentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::component('components.navigationItem', 'navigation-item');

        Blade::component('layouts.app', 'app-layout');
        Blade::component('layouts.simple', 'simple-layout');
    }
}
