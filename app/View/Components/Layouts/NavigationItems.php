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

    public function render(): \Illuminate\Contracts\View\View|string
    {
        return view('components.layouts.navigation-items', [
            'routes' => $this->registerRoutes(),
        ]);
    }

    protected function registerRoutes(): array
    {
        return [
            [
                'name' => 'Servers',
                'url' => route('servers.index'),
                'icon' => 'heroicon-s-server',
                'active' => $this->isActiveRoute('servers.*'),
            ],
            [
                'name' => 'Accounts',
                'url' => route('accounts.index'),
                'icon' => 'heroicon-s-globe-alt',
                'active' => $this->isActiveRoute('accounts.*'),
            ],
        ];
    }

    protected function isActiveRoute(string $string): bool
    {
        return $this->request->routeIs($string);
    }
}
