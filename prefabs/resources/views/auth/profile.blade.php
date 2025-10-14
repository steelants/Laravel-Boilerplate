<x-layout-app>
    <div class="container-xl">
        <div class="page-header">
            <h1>{{ __('Profile') }}</h1>
        </div>

        <div class="form-group">
            <label class="form-label"><b>{{ __('Name') }}:</b></label>
            <p>{{ $user->name ?? '' }}</p>
        </div>

        <div class="form-group">
            <label class="form-label"><b>{{ __('Email') }}:</b></label>
            <p>{{ $user->email ?? '' }}</p>
        </div>

        <hr/>
        @livewire('user.limitation', ['user' => $user])
        <hr/>

        @if (config('session.driver') == 'database')
            <div>
                <h4>{{ __('Sessions') }}</h4>
                @livewire('session.data-table', [], key('data-table'))
            </div>
        @endif

        <div>
            <h4>{{ __('Change Password') }}</h4>
        </div>

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('put')
            <div class="form-group">
                <label class="form-label" for="password">{{ __('Old password') }}</label>
                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password">

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="newPassword">{{ __('New password') }}</label>
                <input class="form-control @error('newPassword') is-invalid @enderror" id="newPassword" name="newPassword" type="password">

                @error('newPassword')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label" for="newPassword-confirm">{{ __('Confirm password') }}</label>
                <input class="form-control" id="newPassword-confirm" name="newPassword_confirmation" type="password">
            </div>

            <button class="btn btn-primary" type="submit">{{ __('Update') }}</button>
        </form>
    </div>
</x-layout-app>
