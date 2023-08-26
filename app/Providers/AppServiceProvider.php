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
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
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
