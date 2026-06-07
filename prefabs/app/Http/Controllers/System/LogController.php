<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use SteelAnts\LaravelBoilerplate\Helpers\SizeHelper;

class LogController extends BaseController
{
    private const LOG_OPEN_LIMIT = 10 * 1000 * 1000 * 1000;

    public function index()
    {
        $items = [];

        $path = storage_path('logs');

        if (!File::exists($path)) {
            File::makeDirectory($path);
        }

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
        if (config('logging.default') == 'daily') {
            $today = date('Y-m-d');
            $todayLog = storage_path('logs/laravel-' . $today . '.log');
        }

        if (File::exists($todayLog)) {
            if (File::size($todayLog) > self::LOG_OPEN_LIMIT) {
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
            'layout'     => config('boilerplate.layouts.system'),
            'items'      => $items,
            'todayStats' => $todayStats,
        ]);
    }

    public function detail($filename)
    {
        $path = $this->resolveLogPath($filename);

        if (!File::exists($path)) {
            abort(404);
        }

        if (File::size($path) > self::LOG_OPEN_LIMIT) {
            return response()->download($path);
        }

        $lines     = File::lines($path)->all();
        $lineCount = count($lines);

        return view('system.log.detail', [
            'layout'    => config('boilerplate.layouts.system'),
            'lines'     => $lines,
            'lineCount' => $lineCount,
            'filename'  => basename($path),
        ]);
    }

    public function tail(Request $request, $filename)
    {
        $path = $this->resolveLogPath($filename);

        if (!File::exists($path)) {
            abort(404);
        }

        $offset = max(0, (int) $request->input('offset', 0));

        $newLines = File::lines($path)->skip($offset)->values();
        $total    = $offset + $newLines->count();

        return response()->json([
            'lines'  => $newLines->all(),
            'offset' => $total,
        ]);
    }

    public function download($filename)
    {
        $path = $this->resolveLogPath($filename);

        if (File::exists($path)) {
            return response()->download($path);
        } else {
            abort(404);
        }
    }

    public function delete($filename)
    {
        $path = $this->resolveLogPath($filename);

        if (File::exists($path)) {
            File::delete($path);

            return redirect()->route('system.log.index');
        } else {
            abort(404);
        }
    }

    private function resolveLogPath(string $filename): string
    {
        return storage_path('logs') . DIRECTORY_SEPARATOR . basename($filename);
    }

    public function clear(Request $request)
    {
        $path = storage_path('logs');
        $files = glob($path . '/lar*.log');

        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        return redirect()->route('system.log.index')->with('success', __('Logs cleared!'));
    }
}
