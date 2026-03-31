<?php

namespace SteelAnts\LaravelBoilerplate\Traits;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait SupportSystemAdmins
{
    protected function isSystemAdmin(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->id, config('boilerplate.system_admins')),
        )->shouldCache();
    }
}
