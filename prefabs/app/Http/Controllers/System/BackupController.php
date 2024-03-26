<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use App\Jobs\Backup;
use Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class BackupController extends BaseController
{
    public function run()
    {
        Backup::dispatchSync();
        return redirect()->back()->with('success', __('boilerplate::ui.backup-running'));
    }

    public function index()
    {
        $backups = [];
        $path = storage_path('backups');
        foreach (File::allFiles($path. "/") as $file) {
            if (!Str::endsWith($file->getFilename(), ".zip")){
                continue;
            }
            $date = explode("_", str_replace(".zip", "", $file->getFilename()))[0];
            if (empty($backups[$date]['fileSize'])) {
                $backups[$date]['fileSize'] = $file->getSize();
            } else {
                $backups[$date]['fileSize'] += $file->getSize();
            }
            $backups[$date]['fileName'][] = $file->getFilename();
        }

        foreach ($backups as $key => $backup) {
            $backups[$key]['fileSize'] = $this->humanFileSize($backup['fileSize']);
        }

        return view('system.backup.index', ['backups' => $backups]);
    }
    public function download($file_name = null)
    {
        if (!empty($file_name)) {
            $path = storage_path('backups/' . $file_name);
            if (!\File::exists($path)) {
                abort(404);
            }
    
            return response()->download($path);
        }
        
        abort(404);
    }

    public function delete($backup_date)
    {
        foreach ([$backup_date . '_database.zip',$backup_date . '_storage.zip'] as $file) {
            $path = storage_path('backups/' . $file);
            if (!empty($path)) {
                File::delete($path);
            }
        }

        return redirect()->back()->with('success',  __('boilerplate::ui.deleted'));
    }

    private function humanFileSize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}
