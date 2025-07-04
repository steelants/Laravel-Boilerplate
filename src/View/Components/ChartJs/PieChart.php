<?php

namespace SteelAnts\LaravelBoilerplate\View\ChartJs;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PieChart extends Component
{
    public string $uuid;
    public array $labels = [];
    public array $colors = [
        "#FFFFFF",
        "#FFFFCC",
        "#FFFF99",
        "#FFFF66",
        "#FFFF33",
        "#FFFF00",
        "#FFCCFF",
        "#FFCCCC",
        "#FFCC99",
        "#FFCC66",
        "#FFCC33",
        "#FFCC00",
        "#FF99FF",
        "#FF99CC",
        "#FF9999",
        "#FF9966",
        "#FF9933",
        "#FF9900",
        "#FF66FF",
        "#FF66CC",
        "#FF6699",
        "#FF6666",
        "#FF6633",
        "#FF6600",
        "#FF33FF",
        "#FF33CC",
        "#FF3399",
        "#FF3366",
        "#FF3333",
        "#FF3300",
        "#FF00FF",
        "#FF00CC",
        "#FF0099",
        "#FF0066",
        "#FF0033",
        "#FF0000",
        "#CCFFFF",
        "#CCFFCC",
        "#CCFF99",
        "#CCFF66",
        "#CCFF33",
        "#CCFF00",
        "#CCCCFF",
        "#CCCCCC",
        "#CCCC99",
        "#CCCC66",
        "#CCCC33",
        "#CCCC00",
        "#CC99FF",
        "#CC99CC",
        "#CC9999",
        "#CC9966",
        "#CC9933",
        "#CC9900",
        "#CC66FF",
        "#CC66CC",
        "#CC6699",
        "#CC6666",
        "#CC6633",
        "#CC6600",
        "#CC33FF",
        "#CC33CC",
        "#CC3399",
        "#CC3366",
        "#CC3333",
        "#CC3300",
        "#CC00FF",
        "#CC00CC",
        "#CC0099",
        "#CC0066",
        "#CC0033",
        "#CC0000",
        "#99FFFF",
        "#99FFCC",
        "#99FF99",
        "#99FF66",
        "#99FF33",
        "#99FF00",
        "#99CCFF",
        "#99CCCC",
        "#99CC99",
        "#99CC66",
        "#99CC33",
        "#99CC00",
        "#9999FF",
        "#9999CC",
        "#999999",
        "#999966",
        "#999933",
        "#999900",
        "#9966FF",
        "#9966CC",
        "#996699",
        "#996666",
        "#996633",
        "#996600",
        "#9933FF",
        "#9933CC",
        "#993399",
        "#993366",
        "#993333",
        "#993300",
        "#9900FF",
        "#9900CC",
        "#990099",
        "#990066",
        "#990033",
        "#990000"
    ];
    public array $values = [];

    /**
     * Create a new component instance.
     */
    public function __construct(array $dataset)
    {
        $this->uuid = uniqid();
        $this->labels = array_keys($dataset);
        $this->values = array_values($dataset);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.chart-js.pie-chart');
    }
}
