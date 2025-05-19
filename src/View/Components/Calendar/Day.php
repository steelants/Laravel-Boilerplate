<?php

namespace SteelAnts\LaravelBoilerplate\View\Components\Calendar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Day extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $day,
        public bool $active = false,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('boilerplate::components.calendar.day');
    }
}
