<?php

namespace SteelAnts\LaravelBoilerplate\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use SteelAnts\LaravelBoilerplate\Traits\Fileable;

class TaskFixture extends Model
{
    use Fileable;

    protected $table = 'users';

    protected $fillable = ['name', 'email', 'password'];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (self $task) {
            $task->email ??= Str::random(16) . '@example.com';
            $task->password ??= Str::random(32);
        });
    }

    public function filePath(): string
    {
        return 'tasks/' . $this->id;
    }
}
