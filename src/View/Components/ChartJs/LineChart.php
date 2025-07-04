<?php

namespace SteelAnts\LaravelBoilerplate\View\ChartJs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LineChart extends Component
{
    public string $uuid;
    public array $labels = [];
    public array $datasets = [];

    /**
     * Create a new component instance.
     */
    public function __construct(array $datasets, array $labels, array $colors, bool $fill = false)
    {
        $this->uuid =  uniqid();
        $this->labels =  $labels;

        $i = 0;
        foreach ($datasets as $dataset) {
            $this->datasets[$i] = [
                'label' => "plineie-chart-". $this->uuid . $i,
                'data' =>  array_values($dataset),
                'fill' => $fill,
                'tension' => 0.1,
				'borderColor' => $colors[$i],
            ];
            $i++;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.chart-js.line-chart');
    }
}
