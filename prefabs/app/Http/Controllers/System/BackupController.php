<?php

namespace App\Http\Controllers\System;

use SteelAnts\LaravelBoilerplate\Helpers\SizeHelper;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SteelAnts\LaravelBoilerplate\Jobs\Backup;

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

        if (file_exists($path)) {
            foreach (File::allFiles($path. "/") as $file) {
                if (!Str::endsWith($file->getFilename(), ".zip")) {
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
                $backups[$key]['fileSize'] = SizeHelper::getHumanReadableSize($backup['fileSize']);
            }
        }

        return view('system.backup.index', ['backups' => $backups]);
    }
    public function download($file_name = null)
    {
        if (!empty($file_name)) {
            $path = storage_path('backups/' . $file_name);
            if (!File::exists($path)) {
                abort(404);
            }

            return response()->download($path);
        }

        abort(404);
    }

    public function delete($backup_date)
    {
        foreach ([$backup_date . '_database.zip', $backup_date . '_storage.zip'] as $file) {
            $path = storage_path('backups/' . $file);
            if (!empty($path)) {
                File::delete($path);
            }
        }

        return redirect()->back()->with('success', __('boilerplate::ui.deleted'));
    }
}
