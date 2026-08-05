<?php

namespace SteelAnts\LaravelBoilerplate\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SteelAnts\LaravelBoilerplate\Facades\FileStorage;
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

    public function getLink(?bool $public = null): string
    {
        $public ??= FileStorage::resolveDisk($this->path, $this->filename) === 'public';

        return FileStorage::loadFile($this->filename, $this->path, $public);
    }
}
