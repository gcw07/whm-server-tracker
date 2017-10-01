<?php

namespace App\Providers;

use App\Connectors\ServerConnector;
use App\Connectors\WHMServerConnector;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Horizon\Horizon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ServerConnector::class, WHMServerConnector::class);

        Horizon::auth(function ($request) {
            if ($request->user()) {
                $config = config('server-tracker');

                if (in_array($request->user()->email, $config['horizon_admin_emails'])) {
                    return true;
                }
            }

            return false;
        });
    }
}
