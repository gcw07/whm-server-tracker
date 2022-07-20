<?php

namespace App\View\Components\Navigation;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Dropdown extends Component
{
    public function __construct()
    {
    }

    public function render(): View
    {
        return view('components.navigation.dropdown');
    }
}
