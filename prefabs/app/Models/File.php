<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use SteelAnts\LaravelBoilerplate\Observers\FileObserver;

#[ObservedBy([FileObserver::class])]
class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'path',
        'original_name',
        'size',
        'type',
    ];

    /**
     * Get all of the owning fileable models.
     */
    public function fileable()
    {
        return $this->morphTo();
    }
}
