<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Searchbox extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public $options,
        public $property,
        public $label = '',
        public $autoclose = 'outside',
    ) {
        foreach ($options as $id => $value) {
            $this->options[] = [
                'id'   => $id,
                'name' => $value,
            ];
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.searchbox');
    }
}
