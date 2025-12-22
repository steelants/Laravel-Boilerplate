<?php

namespace SteelAnts\LaravelBoilerplate\View\Components;

use Closure;
use Illuminate\Contracts\View\View;

class SelectboxAjax extends Selectbox
{
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('boilerplate::components.selectbox-ajax');
    }
}
