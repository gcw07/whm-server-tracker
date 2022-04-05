<?php

namespace App\View\Components\Forms;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Button extends Component
{
    public function __construct()
    {
        //
    }

    public function render(): View
    {
        return view('components.forms.button');
    }
}
