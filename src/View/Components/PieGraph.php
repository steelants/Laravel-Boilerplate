<?php

namespace SteelAnts\LaravelBoilerplate\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PieGraph extends Component
{
    public array $datasets = [];
    public array $labels = [];
    public array $options = [];

    public function __construct($data, $labels = [], $colors = [], bool $legend = true)
    {
        $this->datasets[0] = [
            'data'            => [],
            'backgroundColor' => [],
        ];

        foreach ($data as $name => $value) {
            $this->labels[] = $labels[$name] ?? $name;
            $this->datasets[0]['data'][] = $value;
            $this->datasets[0]['backgroundColor'][] = $colors[$name] ?? null;
        }

        $this->options = [
            'animation'           => false,
            'maintainAspectRatio' => false,
            'responsive'          => true,
            'borderWidth'         => 0,
            'plugins'             => [
                'legend' => ['display' => $legend],
            ],
        ];
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('boilerplate::components.pie-graph');
    }
}
