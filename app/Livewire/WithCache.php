<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Cache;

trait WithCache
{
    protected function putCache($namespace, $name, $value)
    {
        $key = $namespace.'.'.$name.'.'.auth()->user()->id;

        Cache::put($key, $value, now()->addMinutes(15));

        return $value;
    }

    protected function getCache($namespace, $name, $default = null)
    {
        $key = $namespace.'.'.$name.'.'.auth()->user()->id;

        if (Cache::has($key)) {
            return Cache::get($key);
        }

        return $default;
    }
}
