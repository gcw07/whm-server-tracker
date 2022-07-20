<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Cache;

trait WithCache
{
    protected function putCache($name, $value)
    {
        $key = $name.'.'.auth()->user()->id;

        Cache::put($key, $value, now()->addMinutes(15));

        return $value;
    }

    protected function getCache($name, $default = null)
    {
        $key = $name.'.'.auth()->user()->id;

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        return $default;
    }
}
