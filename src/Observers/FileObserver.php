<?php

namespace SteelAnts\LaravelBoilerplate\Observers;

use App\Models\File;
use Illuminate\Support\Facades\Storage;

class FileObserver
{
    public function deleting(File $file)
    {
        Storage::delete($file->path);
    }
}
