<?php

namespace App\View\Components\Layouts;

use Illuminate\Http\Request;
use Illuminate\View\Component;

class NavigationItems extends Component
{
    public bool $isMobileMenu;

    public function __construct(public Request $request, bool $isMobileMenu = false)
    {
        $this->isMobileMenu = $isMobileMenu;
    }

    public function render(): \Illuminate\Contracts\View\View|string
    {
        if ($this->isMobileMenu) {
            return view('components.layouts.navigation-mobile-items', [
                'routes' => $this->registerRoutes(),
            ]);
        }

        return view('components.layouts.navigation-items', [
            'routes' => $this->registerRoutes(),
        ]);
    }

    protected function registerRoutes(): array
    {
        return [
            [
                'name' => 'Dashboard',
                'url' => route('dashboard'),
                'icon' => 'heroicon-s-home',
                'active' => $this->isActiveRoute('dashboard'),
                'mobileOnly' => true,
            ],
            [
                'name' => 'Servers',
                'url' => route('servers.index'),
                'icon' => 'heroicon-s-server',
                'active' => $this->isActiveRoute('servers.*'),
                'mobileOnly' => false,
            ],
            [
                'name' => 'Accounts',
                'url' => route('accounts.index'),
                'icon' => 'heroicon-s-globe-alt',
                'active' => $this->isActiveRoute('accounts.*'),
                'mobileOnly' => false,
            ],
        ];
    }

    protected function isActiveRoute(string $string): bool
    {
        return $this->request->routeIs($string);
    }
}
