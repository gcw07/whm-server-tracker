<?php

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextInput extends Component
{
    public string $name;

    public string $id;

    public string $type;

    public ?string $value;

    public function __construct(string $name, string $id = null, string $type = 'text', ?string $value = '')
    {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->type = $type;
        $this->value = old($name, $value ?? '');
    }

    public function render(): View
    {
        return view('components.forms.text-input');
    }
}
