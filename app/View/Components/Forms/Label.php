<?php

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class Label extends Component
{
    public string $for;

    public ?bool $required;

    public ?string $value;

    public function __construct(string $for, ?bool $required = false, ?string $value = '')
    {
        $this->for = $for;
        $this->required = $required;
        $this->value = $value;
    }

    public function render(): View
    {
        return view('components.forms.label');
    }

    public function fallback(): string
    {
        return $this->value ?? Str::ucfirst(str_replace('_', ' ', $this->for));
    }
}
