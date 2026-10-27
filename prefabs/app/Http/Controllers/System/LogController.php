<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use SteelAnts\LaravelBoilerplate\Services\LogService;

class LogController extends BaseController
{
    public function index()
    {
        return view('system.log.index', [
            'layout'     => config('boilerplate.layouts.system'),
            'items'      => LogService::list(),
            'todayStats' => LogService::todayStats(),
        ]);
    }

    public function detail($filename)
    {
        $path = LogService::path($filename) ?? abort(404);

        if (filesize($path) > LogService::MAX_OPEN_SIZE) {
            return response()->download($path);
        }

        $log = LogService::read($path);

        return view('system.log.detail', [
            'layout'   => config('boilerplate.layouts.system'),
            'lines'    => $log['lines'],
            'offset'   => $log['offset'],
            'filename' => basename($path),
        ]);
    }

    public function tail(Request $request, $filename)
    {
        $path = LogService::path($filename) ?? abort(404);

        return response()->json(LogService::read($path, max(0, (int) $request->input('offset', 0))));
    }

    public function download($filename)
    {
        return response()->download(LogService::path($filename) ?? abort(404));
    }

    public function delete($filename)
    {
        if (!LogService::delete($filename)) {
            abort(404);
        }

        return redirect()->route('system.log.index');
    }

    public function clear()
    {
        LogService::clear();

        return redirect()->route('system.log.index')->with('success', __('Logs cleared!'));
    }
}
