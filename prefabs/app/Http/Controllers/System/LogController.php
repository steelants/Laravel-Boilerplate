<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use SteelAnts\LaravelBoilerplate\Helpers\SizeHelper;

class LogController extends BaseController
{
    public function index()
    {
        $items = [];

        $path = storage_path('logs');

        foreach (File::allFiles($path) as $file) {
            $items[] = [
                'fileName'          => $file->getFilename(),
                'humanReadableSize' => SizeHelper::getHumanReadableSize($file->getSize()),
            ];
        }

        $items = array_reverse($items);

        $todayStats = [
            'ERROR'   => 0,
            'WARNING' => 0,
            'INFO'    => 0,
        ];

        $todayLog = storage_path('logs/laravel.log');
        if (config('logging.default') == "daily") {
            $today = date('Y-m-d');
            $todayLog = storage_path('logs/laravel-' . $today . '.log');
        }

        if (File::exists($todayLog)) {
            if (File::size($todayLog) > 1000 * 1000 * 1000 * 1000) {
                $todayStats = [
                    'ERROR'   => '??',
                    'WARNING' => '??',
                    'INFO'    => '??',
                ];
            } else {
                $content = File::get($todayLog);
                $todayStats['ERROR'] = substr_count($content, '.ERROR:');
                $todayStats['WARNING'] = substr_count($content, '.WARNING:');
                $todayStats['INFO'] = substr_count($content, '.INFO:');
            }
        }

		return view('system.log.index', [
			'layout' => config('boilerplate.layouts.system'),
            'items'      => $items,
            'todayStats' => $todayStats,
        ]);
    }

    public function detail($filename)
    {
        $path = storage_path('logs/' . $filename);

        if (File::exists($path)) {
            if (File::size($path) > 1000 * 1000 * 1000 * 1000) {
                return response()->download($path);
            }

            return view('system.log.detail', [
                'content'  => File::get($path),
                'filename' => $filename,
            ]);
        } else {
            abort(404);
        }
    }

    public function download($filename)
    {
        $path = storage_path('logs/' . $filename);

        if (File::exists($path)) {
            return response()->download($path);
        } else {
            abort(404);
        }
    }

    public function delete($filename)
    {
        $path = storage_path('logs/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
            return redirect()->route('system.log.index');
        } else {
            abort(404);
        }
    }

    public function clear(Request $request)
    {
        $path = storage_path('logs');
        $files = glob($path.'/lar*.log');

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        return redirect()->route('system.log.index')->with('success', __('boilerplate::ui.jobs-cleared'));
    }
}
