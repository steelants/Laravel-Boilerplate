<?php

namespace SteelAnts\LaravelBoilerplate\RenderCasts;

use SteelAnts\DataTable\RenderCasts\RenderCast;

class ListNames implements RenderCast
{
    public function render($key, $value, $model)
    {
        return $value->pluck('name')->join(', ');
    }
}
