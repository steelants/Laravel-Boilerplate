<?php

namespace SteelAnts\LaravelBoilerplate\Traits;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use SteelAnts\LaravelBoilerplate\Services\FileService;
use Illuminate\Support\Str;

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

    public function replaceFile(File $fileModel, UploadedFile|TemporaryUploadedFile $file): string
    {
        $rootPath = Str::rtrim(Str::remove($fileModel->filename, $fileModel->path), '/');
        $file_path = $file->storeAs($rootPath, $fileModel->filename);

        $this->files()->updateOrCreate(
            [
                'filename' => $fileModel->filename,
                'path'     => $file_path,
            ],
            [
                'original_name' => $file->getClientOriginalName(),
                'size'          => $file->getSize(),
            ],
        );

        return "";
    }
}
