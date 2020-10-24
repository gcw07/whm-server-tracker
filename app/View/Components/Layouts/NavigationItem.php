<?php

namespace App\View\Components\Layouts;

use Illuminate\View\Component;

class NavigationItem extends Component
{
    /**
     * The url.
     *
     * @var string
     */
    public $href;

    /**
     * Flag if it is a mobile menu item
     *
     * @var boolean
     */
    public $mobile;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.layouts.navigation-item');
    }
}
