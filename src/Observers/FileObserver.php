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
            $key = $file->path;
        } else {
            $path = rtrim($file->path, '/\\');
            $filename = ltrim($file->filename, '/\\');
            $key = $path . DIRECTORY_SEPARATOR . $filename;
        }

        // Disk se nikde nepersistuje, zjistíme, kde soubor reálně leží.
        foreach (['public', 'local'] as $disk) {
            if (Storage::disk($disk)->exists($key)) {
                Storage::disk($disk)->delete($key);

                return;
            }
        }
    }
}
