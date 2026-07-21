<?php

namespace SteelAnts\LaravelBoilerplate\Observers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SteelAnts\LaravelBoilerplate\Models\File;

class FileObserver
{
    public function deleting(File $file)
    {
        $disk = Storage::disk($file->disk ?: 'local');

        if (Str::contains($file->path, $file->filename)) {
            $disk->delete($file->path);
        } else {
            $path = rtrim($file->path, '/');
            $filename = ltrim($file->filename, '/');
            $disk->delete($path . '/' . $filename);
        }
    }
}
