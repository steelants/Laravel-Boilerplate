<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

trait HasUsersPerPage
{
    public function mountHasUsersPerPage()
    {
        $this->itemsPerPage = auth()->user()->limitationSetting ?? 10;
    }
}
