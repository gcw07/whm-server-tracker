<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Redis;

return [

    'timezone' => env('APP_TIMEZONE', 'UTC'),

    'aliases' => Facade::defaultAliases()->merge([
        'Redis' => Redis::class,
    ])->toArray(),

];
