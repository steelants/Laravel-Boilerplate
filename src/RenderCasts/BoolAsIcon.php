<?php

namespace SteelAnts\LaravelBoilerplate\RenderCasts;

use SteelAnts\DataTable\RenderCasts\RenderCast;

class BoolAsIcon implements RenderCast
{
    public function render($key, $value, $model)
    {
        return '<i class="' . ($value ? 'far fa-check-circle text-success' : 'far fa-times-circle text-danger') . '"></i>';
    }
}
