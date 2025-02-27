<?php

namespace SteelAnts\LaravelBoilerplate\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Breadcrumb extends Component
{
    public array $items = [];
    /**
     * Create a new component instance.
     */
    public function __construct(
        array $items
    ) {
        foreach ($items as $name => $value) {
            $this->items[] = [
                'name'   => $name,
                'link'   => $value,
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
