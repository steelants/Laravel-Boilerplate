<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use SteelAnts\LaravelBoilerplate\Traits\HasSettings;

class UserFixture extends Model
{
    use HasSettings;

    protected $table = 'users';

    protected $fillable = ['name'];

    public $timestamps = false;
}
