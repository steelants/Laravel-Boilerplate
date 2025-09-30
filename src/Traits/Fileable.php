<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SteelAnts\LaravelBoilerplate\Services\FileService;

trait Fileable
{
    /**
     * Get all files.
     */
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

	/**
     * Get one latest file.
     */
	public function file()
    {
        return $this->morphOne(File::class, 'fileable')->latestOfMany();
    }

    public function uploadFile(UploadedFile|TemporaryUploadedFile $file, string $rootPath = "", bool $public = false, $tenant = null) : string {
        return FileService::uploadFile(owner: $this, file: $file, rootPath: $rootPath, public: $public, tenant: $tenant);
    }
}
