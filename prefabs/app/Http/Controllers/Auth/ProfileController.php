<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Requests\Auth\CreateApiTokenRequest;
use App\Http\Requests\Auth\RemoveApiTokenRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

class ProfileController extends BaseController
{
    public function index(Request $request)
    {
        return view('auth.profile', [
            'user'     => $request->user(),
            'sessions' => [],
        ]);
    }

    public function update(UpdateUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        if (!Hash::check($validated['password'], auth()->user()->password)) {
            return redirect()->route('profile.index')->with('error', __('Incorrect old password'));
        }

        if (!empty($validated['newPassword']) && !empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['newPassword']);
            unset($validated['newPassword']);
        } else {
            unset($validated['newPassword']);
            unset($validated['password']);
        }
        $request->user()->update($validated);
        return redirect()->route('profile.index')->with('success', __('Updated'));
    }


    public function api(Request $request)
    {
        return view('auth.profile_api', [
            'user'   => $request->user(),
            'tokens' => $request->user()->tokens->all(),
        ]);
    }

    public function createApiToken(CreateApiTokenRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $tokenCreationParameters = [
            "name"      => $validated['token_name'],
            "abilities" => ['*'],
        ];

        if (!empty($validated['expire_at'])) {
            $tokenCreationParameters["expires_at"] = Carbon::parse($validated['expire_at']);
        }

        $newToken = $request->user()->createToken(...$tokenCreationParameters)->plainTextToken;
        return redirect()->route('profile.api')->with([
            'success' => __('Created'),
            'secret'  => $newToken,
        ]);
    }

    public function removeApiToken(RemoveApiTokenRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $request->user()->tokens()->where('id', $validated['token_id'])->first()->delete();
        return redirect()->route('profile.api')->with('success', __('Removed'));
    }
}
