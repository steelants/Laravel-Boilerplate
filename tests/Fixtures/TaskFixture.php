<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use SteelAnts\LaravelBoilerplate\Traits\Fileable;

class TaskFixture extends Model
{
    use Fileable;

    protected $table = 'users';

    protected $fillable = ['name'];

    public $timestamps = false;

    public function filePath(): string
    {
        return 'tasks/' . $this->id;
    }
}
