<?php

namespace App\Http\Controllers\System;

use App\Models\User;
use App\Http\Requests\System\RemoveUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return view('system.user.index', ['users' => User::all()]);
    }

    public function remove(RemoveUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        User::find($validated['user_id'])->delete();

        return redirect()->route('system.user.index')->with('success',  __('boilerplate::ui.removed'));
    }

}