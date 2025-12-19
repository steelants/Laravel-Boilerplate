<?php

namespace SteelAnts\LaravelBoilerplate\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Selectbox extends Component
{
    public $options = [];

    /**
     * Create a new component instance.
     */
    public function __construct(
        $options,
        public $property = null,
        public $selected = null,
        public $label = null,
        public $innerLabel = null,
        public $placeholder = null,
        public $variant = null, // select, pill, tags
        public $autoclose = 'outside',
        public $multiple = false,
        public $searchable = null,
        public $pills = 2,
        public $class = 'form-select d-flex align-items-center gap-2',
        public $groupClass = '',
        public $selectedGroupClass = 'd-inline-flex flex-wrap gap-1',
        public $id = '',
        public $required = false,
    ) {
		$this->variant = $multiple ? 'tags' : 'select';

        foreach ($options as $id => $value) {
            if (is_array($value)) {
                $this->options[] = $value;
            } else {
                $this->options[] = [
                    'id'   => $id,
                    'name' => $value,
                ];
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('boilerplate::components.selectbox');
    }
}
