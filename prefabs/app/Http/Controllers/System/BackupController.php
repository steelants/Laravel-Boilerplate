<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BackupController extends BaseController
{
    public function run()
    {
        return redirect()->back();
    }

    public function index()
    {
        $backups = [];
        $path = storage_path('backups');
        foreach (File::allFiles($path. "/") as $file) {
            if (!Str::endsWith($file->getFilename(), ".zip")){
                continue;
            }
            $date = explode("_", str_replace(".zip", "", $file->getFilename()))[1];
            $backups[$date]['fileSize'] = $backups[$date]['fileSize'] + $file->getSize();
            $backups[$date]['fileName'][] = $file->getFilename();
        }

        foreach ($backups as $key => $backup) {
            $backups[$key]['fileSize'] = $this->humanFileSize($backup['fileSize']);
        }

        return view('system.backup.index', ['backups' => $backups]);
    }
    public function download($file_name = null)
    {
        abort(404);
    }

    public function delete($file_name)
    {
        $files = explode(",", str_replace(" ", "", $file_name));
        foreach ($files as $file) {
            $path = storage_path('backups/' . $file);
            if (!empty($path)) {
                File::delete($path);
            }
        }
        return redirect()->back();
    }

    private function humanFileSize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}
