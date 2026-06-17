<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use SteelAnts\LaravelBoilerplate\Helpers\SizeHelper;
use SteelAnts\LaravelBoilerplate\Jobs\Backup;
use SteelAnts\LaravelBoilerplate\Jobs\Restore;

class BackupController extends BaseController
{
    public function run()
    {
        Backup::dispatchSync();

        return redirect()->back()->with('success', __('Backup is running'));
    }

    public function index()
    {
        $backups = [];
        $path = storage_path('backups');

        if (file_exists($path)) {
            foreach (File::allFiles($path . '/') as $file) {
                if (!Str::endsWith($file->getFilename(), '.zip')) {
                    continue;
                }
                $date = explode('_', str_replace('.zip', '', $file->getFilename()))[0];
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

        return view('system.backup.index', [
            'layout'  => config('boilerplate.layouts.system'),
            'backups' => $backups,
        ]);
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

        return redirect()->back()->with('success', __('Deleted'));
    }

    public function restore(Request $request, string $backup_date)
    {
        $restoreDb = $request->boolean('restore_database');
        $restoreStorage = $request->boolean('restore_storage');
        $restoreEnv = $request->boolean('restore_env');

        if (!$restoreDb && !$restoreStorage && !$restoreEnv) {
            return redirect()->back()->with('error', __('Select at least one component to restore'));
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $backup_date)->startOfDay();
        } catch (\Exception $e) {
            abort(404);
        }

        $dbZip = storage_path('backups/' . $date->format('Y-m-d') . '_database.zip');
        $fsZip = storage_path('backups/' . $date->format('Y-m-d') . '_storage.zip');

        if ($restoreDb && !File::exists($dbZip)) {
            abort(404);
        }
        if (($restoreStorage || $restoreEnv) && !File::exists($fsZip)) {
            abort(404);
        }

        Restore::dispatchSync($date, $restoreDb, $restoreStorage, $restoreEnv);

        return redirect()->back()->with('success', __('Restore completed'));
    }
}
