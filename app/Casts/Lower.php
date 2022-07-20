<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes;
use Illuminate\Support\Str;

class Lower implements CastsInboundAttributes
{
    public function set($model, $key, $value, $attributes): string
    {
        return Str::lower($value);
    }
}
