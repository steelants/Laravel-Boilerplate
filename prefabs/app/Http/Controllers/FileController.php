<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function serv(string $path = "", string $file_name = "", bool $public = false)
    {
        $file_group_path = str_replace('-', DIRECTORY_SEPARATOR, $path);
		$disk = !empty($public) ? 'public' : 'local';
        $path = trim($file_group_path . DIRECTORY_SEPARATOR . $file_name, DIRECTORY_SEPARATOR);

        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        $absPath = Storage::disk($disk)->path($path);

        if (!is_file($absPath)) {
            abort(404);
        }

        return response()->file($absPath);
    }
}
