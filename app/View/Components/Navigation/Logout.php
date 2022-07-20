<?php

namespace App\View\Components\Navigation;

use Illuminate\View\Component;

class Logout extends Component
{
    public string $action;

    public function __construct(string $action = null)
    {
        $this->action = $action ?? route('logout');
    }

    public function render()
    {
        return view('components.navigation.logout');
    }
}
