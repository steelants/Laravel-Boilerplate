<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateUserRequest;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        return view('auth.profile', [
            'user' => $request->user(),
            'sessions' => [],
        ]);
    }

    public function update(UpdateUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        if (!empty($validated['newPassword']) && !empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['newPassword']);
            unset($validated['newPassword']);
        } else {
            unset($validated['newPassword']);
            unset($validated['password']);
        }
        $request->user()->update($validated);
        return redirect()->route('profile')->with('success', __('ui.Updated'));
    }
}