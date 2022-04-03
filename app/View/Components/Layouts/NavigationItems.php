<?php

namespace App\View\Components\Layouts;

use Illuminate\Http\Request;
use Illuminate\View\Component;

class NavigationItems extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public Request $request)
    {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.layouts.navigation-items', [
            'routes' => $this->registerRoutes(),
        ]);
    }

    protected function registerRoutes()
    {
        return [
            [
                'name' => 'Servers',
                'url' => route('servers.index'),
                'active' => $this->isActiveRoute('servers.*'),
            ],
            [
                'name' => 'Accounts',
                'url' => route('accounts.index'),
                'active' => $this->isActiveRoute('accounts.*'),
            ],
        ];
    }

    protected function isActiveRoute(string $string): bool
    {
        return $this->request->routeIs($string);
    }
}
