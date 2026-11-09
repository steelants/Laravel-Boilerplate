<?php

namespace SteelAnts\LaravelBoilerplate\RenderCasts;

use SteelAnts\DataTable\RenderCasts\RenderCast;

class BoolAsIcon implements RenderCast
{
    public function render($key, $value, $model)
    {
        return '<i class="' . ($value ? 'fa-regular fa-check-circle text-success' : 'fa-regular fa-times-circle text-danger') . '"></i>';
    }
}
