<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SearchBox extends Component
{
    public array $options = [];
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $label,
        public string $property,
        array $options,
    ) {
        foreach ($options as $id => $value) {
            $this->options[] = ['id' => $id, 'name' => $value];
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.search-box');
    }
}
