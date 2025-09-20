<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function serv($file_group = "", $file_name)
    {
        $file_group_path = str_replace('-', DIRECTORY_SEPARATOR, $file_group);
        $path = Storage::path(DIRECTORY_SEPARATOR . $file_group_path . DIRECTORY_SEPARATOR . $file_name);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        return response()->make($file, 200)->header("Content-Type", $type);
    }
}
