<?php

namespace SteelAnts\LaravelBoilerplate\RenderCasts;

use Carbon\Carbon;
use SteelAnts\DataTable\RenderCasts\RenderCast;

class FormatDate implements RenderCast
{
    public function render($key, $value, $model)
    {
        if (!$value) {
            return '';
        }

        return ($value instanceof Carbon ? $value : Carbon::parse($value))->format('Y-m-d');
    }
}
