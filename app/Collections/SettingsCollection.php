<?php

namespace App\Collections;

use Illuminate\Support\Collection;

class SettingsCollection extends Collection
{
    public function merge($items): static
    {
        $this->items = array_merge($this->items, $this->getArrayableItems($items));

        return $this;
    }

    public function forgetAll(): static
    {
        $this->each(fn ($item, $key) => $this->forget($key));

        return $this;
    }
}
