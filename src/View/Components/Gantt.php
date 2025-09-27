<?php

namespace SteelAnts\LaravelBoilerplate\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\View\Component;

class Gantt extends Component
{
    public $steps;
    public $dateFrom;
    public $dateTo;
    public $scale;
    public $rows = [];
    public $stepWidth = 30;
    public $secondsPerStep = 30;

    /**
     * Create a new component instance.
     */
    public function __construct(
        $dateFrom,
        $dateTo,
        $scale = 'month',
        $rows = [],
    ) {
        $this->scale = $scale;

        $this->dateFrom = Carbon::parseFromLocale($dateFrom)->format('Y-m-d 00:00:00');
        $this->dateTo = Carbon::parseFromLocale($dateTo)->format('Y-m-d 23:59:59');

        $this->stepWidth = match ($scale) {
            'day' => 30,
            'week' => 40,
            'month' => 20,
        };

        $this->secondsPerStep = match ($scale) {
            'day' => 60 * 60,
            'week' => 24 * 60 * 60,
            'month' => 24 * 60 * 60,
        };

        $this->steps = $this->generateSteps();

        $this->rows = $this->setupItems($rows);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('boilerplate::components.gantt');
    }

    private function generateSteps()
    {
        $steps = [];

        if ($this->scale == 'day') {
            for ($currentDate = strtotime($this->dateFrom); $currentDate <= strtotime($this->dateTo); $currentDate += $this->secondsPerStep) {
                $group = date('Y-m-d', $currentDate);
                $steps[$group][] = [
                    'title'      => date('H', $currentDate),
                    'isInactive' => date('N', $currentDate) >= 6,
                    'isActive'   => date('Y-m-d H') == date('Y-m-d H', $currentDate),
                ];
            }
        } else if ($this->scale == 'week') {
            for ($currentDate = strtotime($this->dateFrom); $currentDate <= strtotime($this->dateTo); $currentDate += $this->secondsPerStep) {
                $group = date('Y-W', $currentDate);
                $steps[$group][] = [
                    'title'      => date('d', $currentDate),
                    'isInactive' => date('N', $currentDate) >= 6,
                    'isActive'   => date('Y-m-d') == date('Y-m-d', $currentDate),
                ];
            }
        } else if ($this->scale == 'month') {
            for ($currentDate = strtotime($this->dateFrom); $currentDate <= strtotime($this->dateTo); $currentDate += $this->secondsPerStep) {
                $group = date('Y-m', $currentDate);
                $steps[$group][] = [
                    'title'      => date('d', $currentDate),
                    'isInactive' => date('N', $currentDate) >= 6,
                    'isActive'   => date('Y-m-d') == date('Y-m-d', $currentDate),
                ];
            }
        }

        return $steps;
    }

    private function setupItems($rows)
    {
        $ret = [];

        $min = strtotime($this->dateFrom);
        $max = strtotime($this->dateTo);

        foreach ($rows as $row) {
            $items = [];
            foreach ($row['items'] as $key => $item) {
                $from = $this->adjustFrom(strtotime($item['dateFrom']));
                $to = $this->adjustTo(strtotime($item['dateTo']));

                // mimo rozsah
                if ($from > $max || $to < $min) {
                    continue;
                }

                if (isset($item['progress'])) {
                    $item['progressWidth'] = ($item['progress'] / 100) * ($to - $from) * $this->stepWidth / $this->secondsPerStep;

                    // zacatek pred zobrazenym min
                    if ($from < $min) {
                        $item['progressWidth'] -= ($min - $from) * $this->stepWidth / $this->secondsPerStep;
                    }

                    // konec po zobrazenym max
                    if ($to > $max) {
                        $item['progressWidth'] -= ($to - $max) * $this->stepWidth / $this->secondsPerStep;
                    }
                }

                $from = max($from, $min);
                $to = min($to, $max);

                $item['left'] = ($from - $min) * $this->stepWidth / $this->secondsPerStep;
                $item['width'] = ($to - $from) * $this->stepWidth / $this->secondsPerStep;

                $item['color'] ??= 'secondary';

                $items[$key] = $item;
            }

            $row['items'] = $items;
            $ret[] = $row;
        }

        return $ret;
    }

    private function adjustFrom($from)
    {
        return $from;

        // if($this->scale == 'day'){
        //     return strtotime(date('Y-m-d H:00:00', $from));
        // }

        // return strtotime(date('Y-m-d 00:00:00', $from));
    }

    private function adjustTo($to)
    {
        return $to;

        // if($this->scale == 'day'){
        //     return strtotime(date('Y-m-d H:59:59', $to));
        // }

        // return strtotime(date('Y-m-d 23:59:59', $to));
    }
}
