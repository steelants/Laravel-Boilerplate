<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use SteelAnts\LaravelBoilerplate\Traits\Fileable;
use SteelAnts\LaravelBoilerplate\Traits\HasSettings;

class UserFixture extends Model
{
    use Fileable, HasSettings;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (self $user) {
            $user->email ??= Str::random(16) . '@example.com';
            $user->password ??= Str::random(32);
        });
    }
}
