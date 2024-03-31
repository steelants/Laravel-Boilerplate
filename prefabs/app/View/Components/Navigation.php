<?php

namespace App\View\Components;

use App\Menus\MainMenu;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use SteelAnts\LaravelBoilerplate\Facades\Menu;

class Navigation extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navigation', [
            'menuItems' => Menu::get('system-menu')
        ]);
    }
}
