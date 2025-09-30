<?php

namespace SteelAnts\LaravelBoilerplate\Observers;

use Illuminate\Support\Facades\Storage;
use SteelAnts\LaravelBoilerplate\Models\File;

class FileObserver
{
    public function deleting(File $file)
    {
        Storage::delete($file->path);
    }
}
