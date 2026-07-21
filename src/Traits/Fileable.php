<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use Illuminate\Http\UploadedFile;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SteelAnts\LaravelBoilerplate\Models\File;
use SteelAnts\LaravelBoilerplate\Support\FileService;

trait Fileable
{
    /**
     * Get all files.
     */
    public function files()
    {
        return $this->morphMany(config('boilerplate.models.file', File::class), 'fileable');
    }

    /**
     * Get one latest file.
     */
    public function file()
    {
        return $this->morphOne(config('boilerplate.models.file', File::class), 'fileable')->latestOfMany();
    }

    public function uploadFile(UploadedFile|TemporaryUploadedFile $file, string $rootPath = '', bool $public = false): string
    {
        return app(FileService::class)->uploadFile(owner: $this, file: $file, rootPath: $rootPath, public: $public);
    }

    public function replaceFile(File $fileModel, UploadedFile|TemporaryUploadedFile $file, bool $public = false): string
    {
        return app(FileService::class)->replaceFile($fileModel, $file, $public);
    }
}
