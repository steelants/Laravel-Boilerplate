<?php

namespace SteelAnts\LaravelBoilerplate\Support;

use Exception;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Collection;

class MenuItemAction extends MenuItem
{
    public function __construct(public string $title, public string $id, public string $action, public string $icon, public array $parameters = [])
    {
    }
}
