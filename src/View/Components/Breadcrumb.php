<?php

namespace SteelAnts\LaravelBoilerplate\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public $items = [];

    /**
     * Create a new component instance.
     */
    public function __construct(
        $items
    ) {
        foreach ($items as $link => $name) {
            $this->items[] = [
                'name'   => $name,
                'link'   => url($link) ?? null,
            ];
        }

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('boilerplate::components.breadcrumb');
    }
}
