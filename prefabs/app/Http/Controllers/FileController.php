<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function serv(string $file_group = "", string $file_name = "", bool $public = false)
    {
        $file_group_path = str_replace('-', DIRECTORY_SEPARATOR, $file_group);
		$drive = !empty($public) ? 'public' : 'local';
        $path = Storage::drive($drive)->path(DIRECTORY_SEPARATOR . $file_group_path . DIRECTORY_SEPARATOR . $file_name);

        if (!File::exists($path)) {
            abort(404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        return response()->make($file, 200)->header("Content-Type", $type);
    }
}
