<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Models\Activity;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class AuditController extends Controller
{
    public function index()
    {
        //TODO: Clean Up and pagination
        $activities = Activity::with(["performed", "user"])->get();
        $urls = [];

        foreach ($activities as $activity) {
            if (!Str::contains($activity->lang_text, "delete")) {
                // if(!empty($activity->user)) {
                //     $urls[$activity->id] = route('admin.users.update', ['user' => $activity->user]);
                //     continue;
                // }
                $urls[$activity->id] = "";
            }
        }

        return view('system.audit.index', [
            'activities' => $activities,
            'urls' => $urls,
        ]);
    }
}