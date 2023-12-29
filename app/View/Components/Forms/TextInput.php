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

    public ?string $errorField;

    public string $errorBag;

    public function __construct(string $name, ?string $id = null, string $type = 'text', ?string $value = '', ?string $errorField = '', string $errorBag = 'default')
    {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->type = $type;
        $this->value = old($name, $value ?? '');
        $this->errorField = $errorField ?: $name;
        $this->errorBag = $errorBag;
    }

    public function render(): View
    {
        return view('components.forms.text-input');
    }

    public function hasErrors(): bool
    {
        $errors = session()->get('errors');

        if ($errors) {
            $bag = $errors->getBag($this->errorBag);
            ray($bag->has($this->errorField));

            return $bag->has($this->errorField);
        }

        return false;
    }
}
