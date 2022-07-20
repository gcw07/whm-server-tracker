<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        $key = $this->databaseEncryptionKey();
        $cipher = config('app.cipher');

        Model::encryptUsing(new Encrypter($key, $cipher));
    }

    protected function databaseEncryptionKey(): ?string
    {
        $key = config('database.encryption_key');

        return base64_decode(Str::after($key, 'base64:'));
    }
}
