<?php

namespace SteelAnts\LaravelBoilerplate\View\Components\Calendar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Frame extends Component
{
    public array $times = [];
    public string $selectedTime = '';

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $type = 'classic',
        public int $hourFrom = 6,
        public int $hourTo = 20,
    ) {
        $this->times = $this->generateTimes();
        $this->selectedTime = date('H') . ':' . sprintf('%02d', 30 * round(date('i') / 30));
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('boilerplate::components.calendar.frame');
    }

    protected function generateTimes()
    {
        $times = [];

        for ($i = $this->hourFrom * 60; $i <= $this->hourTo * 60; $i += 30) {
            $times[] = sprintf('%02d', $i / 60) . ':' . sprintf('%02d', $i % 60);
        }

        return $times;
    }
}
