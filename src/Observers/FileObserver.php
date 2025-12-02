<?php

namespace SteelAnts\LaravelBoilerplate\Observers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SteelAnts\LaravelBoilerplate\Models\File;

class FileObserver
{
    public function deleting(File $file)
    {
        if (Str::contains($file->path, $file->filename)) {
            Storage::delete($file->path);
        } else {
            $path = rtrim($file->path, '/\\');      // odstraní pouze trailing slash
            $filename = ltrim($file->filename, '/\\');  // odstraní pouze leading slash
            Storage::delete($path . DIRECTORY_SEPARATOR . $filename);
        }
    }
}
