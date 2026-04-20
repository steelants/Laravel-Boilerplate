<?php

namespace SteelAnts\LaravelBoilerplate\RenderCasts;

use SteelAnts\DataTable\RenderCasts\RenderCast;

class EnumLabel implements RenderCast
{
    public function render($key, $value, $model)
    {
        return $value->label();
    }
}
