<?php

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;

class EmailInput extends TextInput
{
    public function __construct(string $name, ?string $id = null, ?string $value = '')
    {
        parent::__construct($name, $id, 'email', $value);
    }

    public function render(): View
    {
        return view('components.forms.email-input');
    }
}
