<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\UpdateUserRequest;
use App\Http\Requests\Auth\CreateApiTokenRequest;
use App\Http\Requests\Auth\RemoveApiTokenRequest;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class ProfileController extends BaseController
{
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
        return redirect()->route('profile')->with('success',  __('boilerplate::ui.updated'));
    }


    public function api(Request $request){
        return view('auth.profile_api', [
            'user' => $request->user(),
            'tokens' => $request->user()->tokens->all(),
        ]);
    }

    public function createApiToken(CreateApiTokenRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $newToken = $request->user()->createToken($validated['token_name'])->plainTextToken;
        return redirect()->route('profile.api')->with([
            'success'=>  __('boilerplate::ui.created'),
            'secret'=> $newToken,
        ]);
    }

    public function removeApiToken(RemoveApiTokenRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $request->user()->tokens()->where('id',  $validated['token_id'])->first()->delete();
        return redirect()->route('profile.api')->with('success',  __('boilerplate::ui.removed'));
    }
}