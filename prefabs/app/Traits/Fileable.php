<?php

namespace App\Traits;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

trait Fileable
{
    /**
     * Get all files.
     */
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function uploadFile(UploadedFile|TemporaryUploadedFile $file, string $rootPath) : string {
        $filename = Str::uuid()->toString() . "." . $file->getClientOriginalExtension();
        $file_path = $file->storeAs($rootPath, $filename);

        $this->files()->updateOrCreate(
            [
                'filename' => $filename,
                'path' => $file_path,
            ],
            [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ],
        );

        return "";
    }
}