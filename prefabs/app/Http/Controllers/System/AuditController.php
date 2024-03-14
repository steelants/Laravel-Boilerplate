<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\BaseController;

class AuditController extends BaseController
{
    public function index()
    {
        //TODO: Clean Up and pagination
        $activities = Activity::with(["affected", "user"])->orderByDesc("created_at")->get();
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
