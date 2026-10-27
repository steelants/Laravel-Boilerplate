<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\File;
use SteelAnts\LaravelBoilerplate\Services\BackupService;

class BackupController extends BaseController
{
    public function index()
    {
        return view('system.backup.index', [
            'layout'  => config('boilerplate.layouts.system'),
            'backups' => BackupService::list(),
            'running' => BackupService::isInProgress(),
        ]);
    }

    public function download(string $file_name)
    {
        // basename() keeps the path inside the backups directory.
        $path = storage_path('backups/' . basename($file_name));
        if (!File::exists($path)) {
            abort(404);
        }

        return response()->download($path);
    }

    public function delete($backup_date)
    {
        BackupService::delete($backup_date);

        return redirect()->back()->with('success', __('Deleted'));
    }
}
