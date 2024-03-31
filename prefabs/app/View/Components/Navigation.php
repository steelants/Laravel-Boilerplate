<?php

namespace App\View\Components;

use App\Menus\MainMenu;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Navigation extends Component
{
    protected MainMenu $mainMenu;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        dump((new MainMenu));
        die();
        $this->mainMenu = (new MainMenu)->toHtml();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navigation');
    }
}
